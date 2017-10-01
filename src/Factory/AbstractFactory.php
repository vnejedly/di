<?php
namespace Hooloovoo\DI\Factory;

/**
 * Class AbstractFactory
 */
abstract class AbstractFactory implements FactoryInterface
{
    /** @var mixed */
    protected $_singleton;

    /**
     * @return mixed
     */
    abstract public function getNew();

    /**
     * @return mixed
     */
    public function getSingleton()
    {
        if (is_null($this->_singleton)) {
            $this->_singleton = $this->getNew();
        }

        return $this->_singleton;
    }

    /**
     * Refreshes singleton
     */
    public function refreshSingleton()
    {
        $this->_singleton = $this->getNew();
    }
}