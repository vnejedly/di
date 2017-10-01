<?php
namespace Hooloovoo\DI\ObjectHolder;

/**
 * Class Unique
 */
class Unique extends AbstractExplicit
{
    /**
     * @param string $className
     * @return object
     */
    public function getObject(string $className)
    {
        return $this->getNewObject($className);
    }
}
