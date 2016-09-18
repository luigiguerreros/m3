<?php
namespace M3\Form\Control;


use M3;

class Checkbox extends ControlAbstract {
    protected $extra_properties = [
        'style_flat' => false,  // true si dibuja el label al lado del widget
    ];

    function set($value) 
    {
        $this->value = (bool)$value;
    }

    function draw() 
    {
        // El checkbox field se dibuja de forma distinta: El label
        // va después del widget.

        $vars = [
            'type' => 'checkbox',
            'name' => $this->name,
            'id' => $this->id(),
        ];

        if ($this->value) {
            $vars ['checked'] = 'true';
        }

        $widget = $this->htmltag ( 'input', $vars );

        if ( $this->style_flat ) {
            $this->draw_label = false;
            $label = $this->drawLabel(false);

            $control = $widget . ' ' . $label;
        }
        else {
            $this->draw_label = true;
            $control = $widget;
        }

        
        return $control;
    }

    function validate() {
        // Este control simepre es válido
        return true;
    }
}