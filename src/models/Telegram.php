<?php

namespace ZakharovAndrew\user\models;

use Yii;
use ZakharovAndrew\user\models\User;

/**
 * Telegram Bot API wrapper for sending messages and media to Telegram groups
 * 
 * @link https://github.com/ZakharovAndrew/yii2-user/
 * @copyright Copyright (c) 2023-2025 Zakharov Andrew
 */
class Telegram extends \yii\base\Model
{
    private $token;
    
    const BASE_API_URL = 'https://api.telegram.org/bot';
    
    function __construct($token) {
        $this->token = $token;
    }

    /**
     * Send text message to Telegram chat
     * 
     * @param int $chatID Chat ID
     * @param string $message Message text
     * @param array $params Additional parameters (parse_mode, etc.)
     * @return array
     */
    public function sendMessage($chatID, $message, $params = [])
    {
        $url = self::BASE_API_URL . $this->token . "/sendMessage";
        
        $data = array_merge([
            'chat_id' => $chatID,
            'text' => $message,
            'parse_mode' => 'HTML'
        ], $params);
        
        return $this->sendRequest($url, $data);
    }
    
    /**
     * Send group of images as media group
     * 
     * @param int $chatID Chat ID
     * @param array $images Array of image paths or URLs
     * @param string $caption Caption for media group
     * @return array
     */
    public function sendMediaGroup($chatID, $images, $caption = '')
    {
        $url = self::BASE_API_URL . $this->token . "/sendMediaGroup";
        
        // Prepare media group
        $media = [];
        $files = [];
        
        foreach ($images as $index => $image) {
            $mediaItem = [
                'type' => 'photo',
                'media' => $this->isUrl($image) ? $image : 'attach://file_' . $index
            ];
            
            // Add caption only to the first image
            if ($index === 0 && !empty($caption)) {
                $mediaItem['caption'] = $caption;
                $mediaItem['parse_mode'] = 'HTML';
            }
            
            $media[] = $mediaItem;
            
            // If it's a local file, add to files array
            if (!$this->isUrl($image)) {
                $files['file_' . $index] = new \CURLFile($image);
            }
        }
        
        $data = [
            'chat_id' => $chatID,
            'media' => json_encode($media)
        ];
        
        return $this->sendRequest($url, $data, $files);
    }
    
    /**
     * Send single photo to Telegram chat
     * 
     * @param int $chatID Chat ID
     * @param string $image Image path or URL
     * @param string $caption Photo caption
     * @param array $params Additional parameters
     * @return array
     */
    public function sendPhoto($chatID, $image, $caption = '', $params = [])
    {
        $url = self::BASE_API_URL . $this->token . "/sendPhoto";
        
        $data = array_merge([
            'chat_id' => $chatID,
        ], $params);
        
        if (!empty($caption)) {
            $data['caption'] = $caption;
            $data['parse_mode'] = 'HTML';
        }
        
        $files = [];
        if ($this->isUrl($image)) {
            $data['photo'] = $image;
        } else {
            $files['photo'] = new \CURLFile($image);
        }
        
        return $this->sendRequest($url, $data, $files);
    }
    
    /**
     * Send document (file) to Telegram chat
     * 
     * @param int $chatID Chat ID
     * @param string $filePath Path to file
     * @param string $caption File caption
     * @return array
     */
    public function sendDocument($chatID, $filePath, $caption = '')
    {
        $url = self::BASE_API_URL . $this->token . "/sendDocument";
        
        $data = [
            'chat_id' => $chatID,
        ];
        
        if (!empty($caption)) {
            $data['caption'] = $caption;
            $data['parse_mode'] = 'HTML';
        }
        
        $files = [
            'document' => new \CURLFile($filePath)
        ];
        
        return $this->sendRequest($url, $data, $files);
    }
    
    /**
     * Get chat ID by group invite link or username
     * 
     * @param string $groupLink Group invite link or username (with @)
     * @return array
     */
    public function getChatIdByLink($groupLink)
    {
        // Extract username from link if full URL is provided
        $username = $this->extractUsernameFromLink($groupLink);
        
        if (empty($username)) {
            return [
                'success' => false,
                'error' => 'Invalid group link or username'
            ];
        }
        
        $url = self::BASE_API_URL . $this->token . "/getChat";
        
        $data = [
            'chat_id' => $username
        ];
        
        $result = $this->sendRequest($url, $data);
        
        if ($result['success'] && isset($result['data']['id'])) {
            $result['chat_id'] = $result['data']['id'];
            $result['title'] = $result['data']['title'] ?? '';
            $result['type'] = $result['data']['type'] ?? '';
        }
        
        return $result;
    }
    
    /**
     * Extract username from Telegram group link
     * 
     * @param string $link Group invite link or username
     * @return string|null
     */
    private function extractUsernameFromLink($link)
    {
        // If it's already a username with @
        if (strpos($link, '@') === 0) {
            return $link;
        }
        
        // Remove https://t.me/ prefix and extract username
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
        
        if (preg_match('/https?:\/\/web\.telegram\.org\/a\/\#([\-0-9]+)/i', $link, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    
    /**
     * Get chat information by ID or username
     * 
     * @param string|int $chatId Chat ID or username
     * @return array
     */
    public function getChat($chatId)
    {
        $url = self::BASE_API_URL . $this->token . "/getChat";
        
        $data = [
            'chat_id' => $chatId
        ];
        
        return $this->sendRequest($url, $data);
    }
    
    /**
     * Universal method for sending requests via cURL
     * 
     * @param string $url Request URL
     * @param array $data Data to send
     * @param array $files Files to send (if any)
     * @return array
     */
    private function sendRequest($url, $data = [], $files = [])
    {
        $ch = curl_init();
        
        // If there are files, use multipart/form-data
        if (!empty($files)) {
            foreach ($files as $key => $file) {
                $data[$key] = $file;
            }
            
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: multipart/form-data"
            ]);
        } else {
            // Otherwise use application/json
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json"
            ]);
            $data = json_encode($data);
        }
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $errno = curl_errno($ch);
        
        curl_close($ch);
        
        // Parse response
        $result = [
            'success' => false,
            'http_code' => $httpCode,
            'error' => null,
            'data' => null
        ];
        
        if ($errno) {
            $result['error'] = "cURL error ($errno): $error";
        } elseif ($httpCode !== 200) {
            $result['error'] = "HTTP error: $httpCode";
        } else {
            $decodedResponse = json_decode($response, true);
            if ($decodedResponse && $decodedResponse['ok']) {
                $result['success'] = true;
                $result['data'] = $decodedResponse['result'];
            } else {
                $result['error'] = "Telegram API error: " . ($decodedResponse['description'] ?? 'Unknown error');
            }
        }
        
        return $result;
    }
    
    /**
     * Check if string is a valid URL
     * 
     * @param string $string
     * @return bool
     */
    private function isUrl($string)
    {
        return filter_var($string, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * Get bot information
     * 
     * @return array
     */
    public function getMe()
    {
        $url = self::BASE_API_URL . $this->token . "/getMe";
        return $this->sendRequest($url);
    }
    
    /**
     * Get updates (for webhooks)
     * 
     * @param int $offset
     * @param int $limit
     * @param int $timeout
     * @return array
     */
    public function getUpdates($offset = null, $limit = 100, $timeout = 0)
    {
        $url = self::BASE_API_URL . $this->token . "/getUpdates";
        
        $data = [
            'limit' => $limit,
            'timeout' => $timeout
        ];
        
        if ($offset !== null) {
            $data['offset'] = $offset;
        }
        
        return $this->sendRequest($url, $data);
    }
}