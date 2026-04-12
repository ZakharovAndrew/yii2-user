<?php

namespace ZakharovAndrew\user\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\caching\CacheInterface;
use yii\httpclient\Client;
use yii\httpclient\Exception as HttpClientException;
use Psr\Log\LoggerInterface;

/**
 * Telegram Bot API component for Yii2
 * 
 * @package ZakharovAndrew\user\components
 * @author Zakharov Andrew
 */
class Telegram extends Component
{
    /**
     * @var string Bot token from BotFather
     */
    public string $token;
    
    /**
     * @var string API base URL
     */
    public string $apiUrl = 'https://api.telegram.org/bot';
    
    /**
     * @var int Request timeout in seconds
     */
    public int $timeout = 30;
    
    /**
     * @var int Connection timeout in seconds
     */
    public int $connectTimeout = 10;
    
    /**
     * @var int Max retry attempts on failure
     */
    public int $maxRetries = 3;
    
    /**
     * @var int Retry delay in seconds
     */
    public int $retryDelay = 1;
    
    /**
     * @var bool Enable debug mode
     */
    public bool $debug = false;
    
    /**
     * @var CacheInterface|null Cache component for chat info
     */
    public ?CacheInterface $cache = null;
    
    /**
     * @var int Cache duration for chat info (seconds)
     */
    public int $cacheDuration = 3600;
    
    /**
     * @var LoggerInterface|null Logger component
     */
    public ?LoggerInterface $logger = null;
    
    /**
     * @var Client HTTP client instance
     */
    private Client $_httpClient;
    
    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();
        
        if (empty($this->token)) {
            throw new InvalidConfigException('Telegram bot token must be configured.');
        }
        
        $this->initHttpClient();
        
        // Use Yii's cache if not explicitly set
        if ($this->cache === null && Yii::$app->has('cache')) {
            $this->cache = Yii::$app->get('cache');
        }
        
        // Use Yii's logger if not explicitly set
        if ($this->logger === null && Yii::$app->has('log')) {
            $this->logger = Yii::getLogger();
        }
    }
    
    /**
     * Initialize HTTP client
     */
    private function initHttpClient(): void
    {
        $this->_httpClient = new Client([
            'baseUrl' => $this->apiUrl . $this->token,
            'requestConfig' => [
                'format' => Client::FORMAT_JSON,
                'timeout' => $this->timeout,
            ],
            'responseConfig' => [
                'format' => Client::FORMAT_JSON,
            ],
        ]);
    }
    
    /**
     * Change bot token dynamically
     * 
     * @param string $newToken New bot token
     * @param bool $reinitClient Reinitialize HTTP client
     */
    public function setToken(string $newToken, bool $reinitClient = true): void
    {
        $this->token = $newToken;
        
        if ($reinitClient) {
            $this->initHttpClient();
        }
    }
    
    /**
     * Change API URL dynamically
     * 
     * @param string $newApiUrl New API URL
     * @param bool $reinitClient Reinitialize HTTP client
     */
    public function setApiUrl(string $newApiUrl, bool $reinitClient = true): void
    {
        $this->apiUrl = $newApiUrl;
        
        if ($reinitClient) {
            $this->initHttpClient();
        }
    }
    
    /**
     * Send text message to Telegram chat
     * 
     * @param int|string $chatId Chat ID or username
     * @param string $message Message text
     * @param array $params Additional parameters
     * @return array Response data
     * @throws \Exception
     */
    public function sendMessage($chatId, string $message, array $params = []): array
    {
        if (empty($message)) {
            throw new \InvalidArgumentException('Message cannot be empty.');
        }
        
        $data = array_merge([
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
        ], $params);
        
        return $this->sendRequest('sendMessage', $data);
    }
    
    /**
     * Send media group (multiple photos/videos)
     * 
     * @param int|string $chatId Chat ID or username
     * @param array $mediaItems Array of media items (each with 'type', 'media', 'caption')
     * @param string|null $caption Caption for first media (deprecated, use media items instead)
     * @return array
     */
    public function sendMediaGroup($chatId, array $mediaItems, ?string $caption = null): array
    {
        if (empty($mediaItems)) {
            throw new \InvalidArgumentException('Media items cannot be empty.');
        }
        
        $media = [];
        
        foreach ($mediaItems as $index => $item) {
            if (is_string($item)) {
                // Backward compatibility with old format
                $mediaItem = [
                    'type' => 'photo',
                    'media' => $this->isUrl($item) ? $item : 'attach://file_' . $index,
                ];
                
                if ($index === 0 && $caption) {
                    $mediaItem['caption'] = $caption;
                    $mediaItem['parse_mode'] = 'HTML';
                }
            } else {
                // New format with full control
                $mediaItem = $item;
                if (!isset($mediaItem['media'])) {
                    throw new \InvalidArgumentException("Media item must have 'media' field.");
                }
            }
            
            $media[] = $mediaItem;
        }
        
        $data = [
            'chat_id' => $chatId,
            'media' => $media,
        ];
        
        return $this->sendRequest('sendMediaGroup', $data);
    }
    
    /**
     * Send single photo
     * 
     * @param int|string $chatId Chat ID or username
     * @param string $image Image path, URL, or file_id
     * @param string $caption Photo caption
     * @param array $params Additional parameters
     * @return array
     */
    public function sendPhoto($chatId, string $image, string $caption = '', array $params = []): array
    {
        $data = array_merge([
            'chat_id' => $chatId,
        ], $params);
        
        if ($caption) {
            $data['caption'] = $caption;
            $data['parse_mode'] = 'HTML';
        }
        
        if ($this->isUrl($image) || $this->isFileId($image)) {
            $data['photo'] = $image;
            return $this->sendRequest('sendPhoto', $data);
        }
        
        return $this->sendFile('sendPhoto', 'photo', $image, $data);
    }
    
    /**
     * Send document
     * 
     * @param int|string $chatId Chat ID or username
     * @param string $filePath Path to file
     * @param string $caption File caption
     * @return array
     */
    public function sendDocument($chatId, string $filePath, string $caption = ''): array
    {
        $data = ['chat_id' => $chatId];
        
        if ($caption) {
            $data['caption'] = $caption;
            $data['parse_mode'] = 'HTML';
        }
        
        return $this->sendFile('sendDocument', 'document', $filePath, $data);
    }
    
    /**
     * Get chat information with caching
     * 
     * @param string|int $chatId Chat ID or username
     * @param bool $useCache Use cached result
     * @return array
     */
    public function getChat($chatId, bool $useCache = true): array
    {
        if ($useCache && $this->cache !== null) {
            $cacheKey = "telegram_chat_{$chatId}";
            $cached = $this->cache->get($cacheKey);
            
            if ($cached !== false) {
                return $cached;
            }
        }
        
        $result = $this->sendRequest('getChat', ['chat_id' => $chatId]);
        
        if ($result['success'] && $useCache && $this->cache !== null) {
            $this->cache->set($cacheKey, $result, $this->cacheDuration);
        }
        
        return $result;
    }
    
    /**
     * Get chat ID by invite link or username
     * 
     * @param string $groupLink Group invite link or username
     * @return array
     */
    public function getChatIdByLink(string $groupLink): array
    {
        $username = $this->extractUsernameFromLink($groupLink);
        
        if (!$username) {
            return [
                'success' => false,
                'error' => 'Invalid group link or username',
            ];
        }
        
        return $this->getChat($username);
    }
    
    /**
     * Get bot information
     * 
     * @return array
     */
    public function getMe(): array
    {
        return $this->sendRequest('getMe');
    }
    
    /**
     * Send request to Telegram API with retry logic
     * 
     * @param string $method API method
     * @param array $data Request data
     * @return array
     * @throws \Exception
     */
    private function sendRequest(string $method, array $data = []): array
    {
        $attempt = 0;
        $lastError = null;
        
        while ($attempt < $this->maxRetries) {
            try {
                $response = $this->_httpClient->post($method, $data)->send();
                
                $result = [
                    'success' => $response->isOk && !empty($response->data['ok']),
                    'http_code' => $response->statusCode,
                    'error' => null,
                    'data' => $response->data['result'] ?? null,
                ];
                
                if (!$result['success']) {
                    $result['error'] = $response->data['description'] ?? 'Unknown error';
                    
                    // Log error
                    if ($this->logger) {
                        $this->logger->error("Telegram API error: {$result['error']}", [
                            'method' => $method,
                            'data' => $data,
                            'response' => $response->data,
                        ]);
                    }
                }
                
                if ($this->debug && $this->logger) {
                    $this->logger->info("Telegram API request: {$method}", [
                        'data' => $data,
                        'result' => $result,
                    ]);
                }
                
                return $result;
                
            } catch (HttpClientException $e) {
                $lastError = $e->getMessage();
                $attempt++;
                
                if ($attempt < $this->maxRetries) {
                    sleep($this->retryDelay * $attempt); // Exponential backoff
                    continue;
                }
            }
        }
        
        $error = "Telegram API request failed after {$this->maxRetries} attempts: {$lastError}";
        
        if ($this->logger) {
            $this->logger->error($error, ['method' => $method, 'data' => $data]);
        }
        
        return [
            'success' => false,
            'error' => $error,
            'http_code' => null,
            'data' => null,
        ];
    }
    
    /**
     * Send file to Telegram
     * 
     * @param string $method API method
     * @param string $fieldName Field name for file
     * @param string $filePath Path to file
     * @param array $data Additional data
     * @return array
     */
    private function sendFile(string $method, string $fieldName, string $filePath, array $data = []): array
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("File not found: {$filePath}");
        }
        
        $multipartData = [];
        
        foreach ($data as $key => $value) {
            $multipartData[] = ['name' => $key, 'contents' => (string) $value];
        }
        
        $multipartData[] = [
            'name' => $fieldName,
            'contents' => fopen($filePath, 'r'),
            'filename' => basename($filePath),
        ];
        
        try {
            $response = $this->_httpClient->createRequest()
                ->setMethod('POST')
                ->setUrl($method)
                ->setMultipart($multipartData)
                ->send();
            
            return [
                'success' => $response->isOk && !empty($response->data['ok']),
                'http_code' => $response->statusCode,
                'error' => $response->data['description'] ?? null,
                'data' => $response->data['result'] ?? null,
            ];
        } catch (HttpClientException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'http_code' => null,
                'data' => null,
            ];
        }
    }
    
    /**
     * Extract username from Telegram group link
     * 
     * @param string $link
     * @return string|null
     */
    private function extractUsernameFromLink(string $link): ?string
    {
        // Already a username
        if (strpos($link, '@') === 0) {
            return $link;
        }
        
        $patterns = [
            '/https?:\/\/t\.me\/([a-zA-Z0-9_]+)/i',
            '/t\.me\/([a-zA-Z0-9_]+)/i',
            '/^([a-zA-Z0-9_]+)$/',
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $link, $matches)) {
                return '@' . $matches[1];
            }
        }
        
        // Handle private group links
        if (preg_match('/https?:\/\/t\.me\/joinchat\/([a-zA-Z0-9_-]+)/i', $link, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    
    /**
     * Check if string is URL
     * 
     * @param string $string
     * @return bool
     */
    private function isUrl(string $string): bool
    {
        return filter_var($string, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * Check if string is a Telegram file_id
     * 
     * @param string $string
     * @return bool
     */
    private function isFileId(string $string): bool
    {
        // Telegram file_id format (base64-like string, usually 50-200 chars)
        return preg_match('/^[A-Za-z0-9_\-]+$/', $string) && strlen($string) > 20;
    }
}