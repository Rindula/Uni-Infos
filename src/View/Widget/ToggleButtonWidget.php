<?php

namespace App\View\Widget;

use Cake\View\Form\ContextInterface;
use Cake\View\Widget\BasicWidget;

class ToggleButtonWidget extends BasicWidget
{
    protected $_templates;

    protected $defaults = [
        'name' => '',
        'value' => 1,
        'val' => null,
        'disabled' => false,
        'templateVars' => [],
    ];

    public function render(array $data, ContextInterface $context): string
    {
        $data += $this->mergeDefaults($data, $context);

        if ($this->_isChecked($data)) {
            $data['checked'] = true;
        }
        unset($data['val']);

        $attrs = $this->_templates->formatAttributes(
            $data,
            ['name', 'value']
        );

        return $this->_templates->format('toggleButton', [
            'name' => $data['name'],
            'value' => $data['value'],
            'templateVars' => $data['templateVars'],
            'attrs' => $attrs,
        ]);
    }

    protected function _isChecked(array $data): bool
    {
        if (array_key_exists('checked', $data)) {
            return (bool)$data['checked'];
        }

        return (string)$data['val'] === (string)$data['value'];
    }

}
