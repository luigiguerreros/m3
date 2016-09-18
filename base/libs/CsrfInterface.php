<?php
namespace M3;

interface CsrfInterface
{
    /**
     * Generates a new CSRF token
     */
    public function generateToken();

    /**
     * Obtains the generated token
     */
    public function getToken();

    /**
     * Returns the saved CSRF token
     */ 
    public function getSavedToken();
}

