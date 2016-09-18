<?php
namespace M3\Form\Control;

use M3;
use M3\Form;
use M3\Form\Validator;

class Email extends Text
{
    const REGEXP_EMAIL = '^[0-9A-Za-z\-_\!\.]+@[0-9A-Za-z\-]+\.[0-9A-Za-z\.\-]*[0-9A-Za-z]$';

	function __construct( $name, $properties, $form ) {
		parent::__construct( $name, $properties, $form );

		// Activamos el filtro de regexp
		$this->validators = array_merge($this->validators, [
            [Validator\RegExp::class, 'regexp' => self::REGEXP_EMAIL, 'case_insensitive' => true]
        ]);
	}

	public function draw() {
        return parent::draw([
            'type' => 'email',
        ]);
    }
}