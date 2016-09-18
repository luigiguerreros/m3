<?php
namespace M3;

use M3;

/**
 * Decorator-like class to integration with M3
 */
class Form extends Form\Form
{
    public function __construct()
    {
        parent::__construct(new Csrf);
    }

    /**
     * Process forms with POST data.
     */
    function isValidFromPost()
    {
        if (M3::$request->isPost()) {
            $this->setValues(M3::$request->getParsedBody());
            return $this->isValid();
        }
        return false;        
    }

}
