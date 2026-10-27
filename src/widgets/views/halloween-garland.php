<?php
/**
 * Halloween Garland Widget View
 * 
 * This view renders an animated Halloween-themed garland that hangs below the navbar.
 * It displays a rope with hanging elements (pumpkins, bats, ghosts, etc.) and glowing lights.
 * 
 * @var array $elements Array of emoji/HTML elements to display on the garland
 * @var array $lightColors Array of color classes for the lights (orange, purple, green)
 * @var array $itemPositions Array of horizontal positions (%) for each element
 * @var array $lightPositions Array of horizontal positions (%) for each light
 * @var array $itemDelays Array of animation delays (seconds) for elements
 * @var array $lightDelays Array of animation delays (seconds) for lights
 * @var array $flickerItems Array of booleans indicating which elements should flicker
 * @var int $itemsCount Total number of elements in the garland
 * 
 * @link https://github.com/ZakharovAndrew/yii2-user
 * @copyright Copyright (c) 2024 Zakharov Andrey
 */
?>

<style>
    /* Main container for the garland - positioned below navbar */
    .halloween-garland {
        position: relative;
        width: 100%;
        height: 80px;
        background: transparent;
        overflow: visible;
        z-index: 1030; /* Higher than navbar (1000) to appear on top */
        pointer-events: none; /* Allows clicks to pass through to elements below */
    }
    
    /* The rope/string that holds all elements */
    .garland-rope {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, #ff6b00 0%, #ff8c00 25%, #ffa500 50%, #ff8c00 75%, #ff6b00 100%);
        box-shadow: 0 0 10px rgba(255, 140, 0, 0.5);
    }
    
    /* Individual hanging elements (pumpkins, bats, ghosts, etc.) */
    .garland-item {
        position: absolute;
        top: 2px;
        transform-origin: top center;
        animation: garlandSway 3s ease-in-out infinite;
    }
    
    /* Styling for the emoji/HTML elements */
    .garland-element {
        font-size: 40px;
        filter: drop-shadow(0 5px 10px rgba(0, 0, 0, 0.3));
        display: inline-block;
        line-height: 1;
    }
    
    /* Swaying animation for hanging elements */
    @keyframes garlandSway {
        0%, 100% { transform: rotate(-8deg) translateY(0); }
        25% { transform: rotate(-4deg) translateY(-5px); }
        50% { transform: rotate(0deg) translateY(-8px); }
        75% { transform: rotate(4deg) translateY(-5px); }
    }
    
    /* Flickering effect for some elements */
    .flicker {
        animation: garlandSway 3s ease-in-out infinite, flicker 2s ease-in-out infinite;
    }
    
    @keyframes flicker {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    
    /* Glowing lights between elements */
    .garland-light {
        position: absolute;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        top: -3px;
        animation: lightGlow 1.5s ease-in-out infinite alternate;
    }
    
    /* Light color variations */
    .light-orange { background: #ff6b00; box-shadow: 0 0 10px #ff6b00, 0 0 20px #ff6b00; }
    .light-purple { background: #9b59b6; box-shadow: 0 0 10px #9b59b6, 0 0 20px #9b59b6; }
    .light-green { background: #2ecc71; box-shadow: 0 0 10px #2ecc71, 0 0 20px #2ecc71; }
    
    /* Pulsing glow animation for lights */
    @keyframes lightGlow {
        0% { opacity: 0.5; transform: scale(0.8); }
        100% { opacity: 1; transform: scale(1.2); }
    }
    
    /* Responsive adjustments for mobile devices */
    @media screen and (max-width: 768px) {
        .halloween-garland { height: 60px; }
        .garland-element { font-size: 28px; }
        .garland-light { width: 6px; height: 6px; }
    }
</style>

<div class="halloween-garland">
    <!-- The rope that holds all elements -->
    <div class="garland-rope"></div>
    
    <!-- Glowing lights between elements -->
    <?php foreach ($lightColors as $index => $color): ?>
        <div class="garland-light light-<?= $color ?>" 
             style="left: <?= $lightPositions[$index] ?>%; animation-delay: <?= $lightDelays[$index] ?>s;">
        </div>
    <?php endforeach; ?>
    
    <!-- Hanging Halloween elements (pumpkins, bats, ghosts, etc.) -->
    <?php foreach ($elements as $index => $element): ?>
        <div class="garland-item" 
             style="left: <?= $itemPositions[$index] ?>%; animation-delay: <?= $itemDelays[$index] ?>s;">
            <span class="garland-element <?= $flickerItems[$index] ? 'flicker' : '' ?>">
                <?= $element ?>
            </span>
        </div>
    <?php endforeach; ?>
</div>
