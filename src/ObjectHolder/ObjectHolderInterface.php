<?php
namespace Hooloovoo\DI\ObjectHolder;

/**
 * Interface ObjectHolderInterface
 */
interface ObjectHolderInterface
{
    /**
     * @param string $className
     * @return object
     */
    public function getObject(string $className);
}
