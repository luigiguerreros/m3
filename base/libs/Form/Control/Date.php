<?php
namespace M3\Form\Control;

use M3;

/**
 * Campo de Fecha. Dibuja un tag INPUT con type="date".
 */
class Date extends Text
{
    public function draw() 
    {
        return parent::draw([
            'type' => 'date',
        ]);
    }

    /**
     * This controls returns a M3\DateTime object
     */
    public function getValue()
    {
        $value = parent::getValue();
        if ($value) {
            return new M3\DateTime($value);
        } else {
            return null;
        }
    }
}