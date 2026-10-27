<?php

/**
 * @link https://github.com/ZakharovAndrew/yii2-user
 * @copyright Copyright (c) 2024 Zakharov Andrey
 */

namespace ZakharovAndrew\user\widgets;

use Yii;
use yii\base\Widget;

/**
 * HalloweenGarlandWidget displays a festive garland under the navbar during the Halloween period.
 */
class HalloweenGarlandWidget extends Widget
{
    /**
     * @var string Start date for displaying the garland in 'MM-DD' format (default: October 20)
     */
    public $startDate = '10-20';

    /**
     * @var string End date for displaying the garland in 'MM-DD' format (default: November 5)
     */
    public $endDate = '11-05';

    /**
     * @var bool Force display (useful for testing or debugging, overrides date check)
     */
    public $forceShow = false;

    /**
     * @var int Number of elements in the garland
     */
    public $itemsCount = 14;

    /**
     * @var array Types of elements (emojis or HTML strings)
     */
    public $elementTypes = ['🎃', '🦇', '👻', '💀', '🕷️'];

    /**
     * @var array Colors of the garland lights
     */
    public $lightColors = ['orange', 'purple', 'green'];

    /**
     * Checks whether the garland should be displayed based on the current date.
     * 
     * @return bool
     */
    protected function shouldShow(): bool
    {
        if ($this->forceShow || YII_DEBUG) {
            return true;
        }

        $currentDate = date('m-d');
        $start = $this->startDate;
        $end = $this->endDate;

        // Handle periods that cross over the new year (e.g., December to January)
        if ($start > $end) {
            return $currentDate >= $start || $currentDate <= $end;
        }

        return $currentDate >= $start && $currentDate <= $end;
    }

    /**
     * Generates an array of elements in random order.
     * 
     * @return array
     */
    protected function generateElements(): array
    {
        $elements = [];
        for ($i = 0; $i < $this->itemsCount; $i++) {
            $elements[] = $this->elementTypes[array_rand($this->elementTypes)];
        }
        return $elements;
    }

    /**
     * Generates an array of colors for the garland lights.
     * 
     * @return array
     */
    protected function generateLightColors(): array
    {
        $colors = [];
        $lightsCount = $this->itemsCount - 1;
        for ($i = 0; $i < $lightsCount; $i++) {
            $colors[] = $this->lightColors[array_rand($this->lightColors)];
        }
        return $colors;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if (!$this->shouldShow()) {
            return '';
        }

        $elements = $this->generateElements();
        $lightColors = $this->generateLightColors();

        // Generate positions and animation delays
        $itemPositions = [];
        $lightPositions = [];
        $step = 100 / ($this->itemsCount + 1);

        for ($i = 0; $i < $this->itemsCount; $i++) {
            $itemPositions[] = round($step * ($i + 1), 2);
        }

        for ($i = 0; $i < count($lightColors); $i++) {
            $lightPositions[] = round(($itemPositions[$i] + $itemPositions[$i + 1]) / 2, 2);
        }

        $itemDelays = [];
        $lightDelays = [];
        for ($i = 0; $i < $this->itemsCount; $i++) {
            $itemDelays[] = round(($i * 0.3) % 3, 1);
        }
        for ($i = 0; $i < count($lightColors); $i++) {
            $lightDelays[] = round(($i * 0.2) % 1.5, 1);
        }

        // Every 3rd or 5th element will have a flickering effect
        $flickerItems = [];
        for ($i = 0; $i < $this->itemsCount; $i++) {
            $flickerItems[] = ($i % 3 === 0 || $i % 5 === 0);
        }

        return $this->render('halloween-garland', [
            'elements' => $elements,
            'lightColors' => $lightColors,
            'itemPositions' => $itemPositions,
            'lightPositions' => $lightPositions,
            'itemDelays' => $itemDelays,
            'lightDelays' => $lightDelays,
            'flickerItems' => $flickerItems,
            'itemsCount' => $this->itemsCount,
        ]);
    }
}
