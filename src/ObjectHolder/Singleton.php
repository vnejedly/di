<?php
namespace Hooloovoo\DI\ObjectHolder;

/**
 * Class Singleton
 */
class Singleton extends AbstractExplicit
{
    /** @var object */
    protected $_object;

    /**
     * @param string $className
     * @return object
     */
    public function getObject(string $className)
    {
        if (!isset($this->_object)) {
            $this->_object = $this->getNewObject($className);
        }

        return $this->_object;
    }
}
