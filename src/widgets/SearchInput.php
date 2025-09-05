<?php 

namespace ZakharovAndrew\user\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;

class SearchInput extends Widget
{
    public function run()
    {
        // Регистрируем JS для обработки связки чекбокс+поле
        //$this->registerJs();
        return $this->renderSearch($this->menu);
    }
    
    protected function renderMenu($items)
    {
        $html = '<div class="search-input-group">';
        
        $html .= Html::input('text', ['id' => 'search-input']);
        
        $html .= '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>';
        $html .= '</div>';
        $html .= '<style>.search-input-group svg {
    position: absolute;
    color: #96a5bd;
    top: 50%;
    left: 0.9rem;
    transform: translateY(-48%);
    height: 17px;
}</style>';
        
        return $html;
    }
}