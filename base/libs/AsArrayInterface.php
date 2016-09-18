<?php
namespace M3;

/**
 * Interface for object that can be converted to array
 */
interface AsArrayInterface
{
    /**
     * Returns an array representation of this object
     */
    public function asArray();
}