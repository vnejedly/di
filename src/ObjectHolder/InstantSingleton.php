<?php
namespace Hooloovoo\DI\ObjectHolder;

/**
 * Class InstantSingleton
 */
class InstantSingleton implements ObjectHolderInterface
{
    /** @var object */
    protected $_object;

    /**
     * InstantSingleton constructor.
     * @param object $object
     */
    public function __construct($object)
    {
        $this->_object = $object;
    }

    /**
     * @param string $className
     * @return object
     */
    public function getObject(string $className)
    {
        return $this->_object;
    }
}