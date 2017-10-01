<?php
namespace Hooloovoo\DI\ObjectHolder;

/**
 * Class AbstractExplicit
 */
abstract class AbstractExplicit implements ObjectHolderInterface
{
    /** @var Callable */
    protected $_callback;

    /**
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->_callback = $callback;
    }

    /**
     * @param string $className
     * @return object
     */
    protected function getNewObject(string $className)
    {
        $callback = &$this->_callback;
        return $callback($className);
    }
}
