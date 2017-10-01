<?php
namespace Hooloovoo\DI\Container;

use Hooloovoo\DI\Definition\DefinitionClassInterface;
use Hooloovoo\DI\ObjectHolder\ObjectHolderInterface;

/**
 * Interface ContainerInterface
 */
interface ContainerInterface
{
    /**
     * @param DefinitionClassInterface $definitionClass
     */
    public function addDefinitionClass(DefinitionClassInterface $definitionClass);

    /**
     * @param string $key
     * @return bool
     */
    public function exists(string $key) : bool;

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key);

    /**
     * @param string $name
     * @param mixed $value
     */
    public function addScalar(string $name, $value);

    /**
     * @param array $scalars
     */
    public function addScalarSet(array $scalars);

    /**
     * @param string $key
     * @param ObjectHolderInterface $holder
     */
    public function add(string $key, ObjectHolderInterface $holder);

    /**
     * @param string $pregKey
     * @param ObjectHolderInterface $holder
     */
    public function addPreg(string $pregKey, ObjectHolderInterface $holder);

    /**
     * @param string $factoryKey
     * @param string $objectKey
     * @param callable $callback
     */
    public function addFactory(string $factoryKey, string $objectKey, callable $callback);

    /**
     * @return ObjectHolderInterface[]
     */
    public function getHolders();

    /**
     * @return ObjectHolderInterface[]
     */
    public function getPregHolders();
}