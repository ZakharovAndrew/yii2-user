<?php 

namespace ZakharovAndrew\user\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;

class CheckboxMenuWithInput extends Widget
{
    public $menu;
    public $model;
    public $attribute;
    public $selectedData = [];

    public function run()
    {
        // Регистрируем JS для обработки связки чекбокс+поле
        $this->registerJs();
        return $this->renderMenu($this->menu);
    }

    protected function renderMenu($items)
    {
        $html = '<div class="checkbox-input-menu">';
        foreach ($items as $item) {
            if (isset($item['items'])) {
                $html .= $this->renderGroup($item);
            } else {
                $html .= $this->renderItem($item);
            }
        }
        $html .= '</div>';
        
        // Скрытое поле для хранения JSON
        $html .= Html::hiddenInput(
            $this->model ? "{$this->model->formName()}[{$this->attribute}]" : $this->attribute,
            Json::encode($this->selectedData),
            ['class' => 'checkbox-input-json']
        );
        
        return $html;
    }

    protected function renderGroup($group)
    {
        $items = '';
        foreach ($group['items'] as $item) {
            $items .= $this->renderItem($item);
        }

        return Html::tag('div', 
            Html::tag('h3', Html::encode($group['label']), ['class' => 'group-title']) . 
            Html::tag('div', $items, ['class' => 'group-items']),
            ['class' => 'menu-group']
        );
    }

    protected function renderItem($item)
    {
        $itemId = $item['id'];
        $isChecked = isset($this->selectedData[$itemId]);
        $textValue = $isChecked ? $this->selectedData[$itemId] : '';
        
        return Html::tag('div',
            Html::checkbox(
                "checkbox_{$itemId}",
                $isChecked,
                [
                    'value' => $itemId,
                    'id' => "checkbox_{$itemId}",
                    'class' => 'menu-checkbox',
                    'data-item-id' => $itemId
                ]
            ) .
            Html::label($item['label'], "checkbox_{$itemId}", ['class' => 'menu-label']) . 
            Html::input('text', 
                "input_{$itemId}", 
                $textValue,
                [
                    'class' => 'menu-input form-control',
                    'placeholder' => 'Введите значение',
                    'data-item-id' => $itemId,
                    'disabled' => !$isChecked
                ]
            ),
            
            ['class' => 'menu-item']
        );
    }

    protected function registerJs()
    {
        $js = <<<JS
$(document)
    .on('change', '.menu-checkbox', function() {
        var itemId = $(this).data('item-id');
        var input = $('.menu-input[data-item-id="' + itemId + '"]');
        input.prop('disabled', !this.checked);

        if (!this.checked) {
            input.val('');
        }

        updateJsonData();
    })
    .on('input', '.menu-input', function() {
        updateJsonData();
    });

function updateJsonData() {
    var result = {};

    $('.menu-checkbox:checked').each(function() {
        var itemId = $(this).data('item-id');
        var inputValue = $('.menu-input[data-item-id="' + itemId + '"]').val();
        if (inputValue) {
            result[itemId] = inputValue;
        } else {
            result[itemId] = "*";
        }
    });

    $('.checkbox-input-json').val(JSON.stringify(result));
                console.log(JSON.stringify(result));
}
JS;
        
        $this->view->registerJs($js);
    }
}