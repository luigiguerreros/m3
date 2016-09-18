<?php
namespace M3\Logger\Target;

class Stream implements TargetInterface
{
    private $loglevel = '';
    private $stream = '';

    public function __construct($loglevel, $stream)
    {
        $this->loglevel = $loglevel;
        $this->stream = $steam;
    }

    public function 
}