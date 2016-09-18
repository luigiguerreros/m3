<?php
namespace M3\Form\Control;

use M3;

/**
 * Campo de Hora. Dibuja un tag INPUT con type="time".
 */
class Time extends ControlAbstract
{
    function draw() {
        return parent::draw([
            'type' => 'time',
            'name' => $this->name,
            'id' => $this->name,
            'value' => $this->value,
        ]);
    }
}