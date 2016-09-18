<?php
namespace M3\Http;

use Psr\Http\Message\Stream as PsrStream;

class Stream extends PsrStream
{
    /**
     * Send all the body directly to PHP output buffer.
     */
    public function passthru()
    {
        $this->rewind();
        fpassthru($this->resource);
    }
}