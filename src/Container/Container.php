<?php
namespace Hooloovoo\DI\Container;

use Hooloovoo\DI\Definition\DefinitionClassInterface;
use Hooloovoo\DI\Exception\AutoResolveException;
use Hooloovoo\DI\Exception\LogicException;
use Hooloovoo\DI\Exception\NewInstanceException;
use Hooloovoo\DI\Factory\FactoryInterface;
use Hooloovoo\DI\ObjectHolder\InstantSingleton;
use Hooloovoo\DI\ObjectHolder\ObjectHolderInterface;
use Hooloovoo\DI\ObjectHolder\Singleton;
use ReflectionClass;

/**
 * Class AbstractContainer
 */
class Container implements ContainerInterface
{
    /** @var ObjectHolderInterface[] */
    protected $objects = [];
    
    /** @var ObjectHolderInterface[] */
    protected $pregObjects = [];

    /** @var ReflectionClass[] */
    protected $resolvedReflections = [];

    /** @var array[] */
    protected $scalars = [];

    /**
     * @param DefinitionClassInterface $definitionClass
     */
    public function addDefinitionClass(DefinitionClassInterface $definitionClass)
    {
        $definitionClass->setUpContainer($this);
    }
    
    /**
     * @param string $key
     * @return bool
     */
    public function exists(string $key) : bool
    {
        return array_key_exists($key, $this->objects);
    }

    /**
     * @param string $key
     * @return object
     */
    public function get(string $key)
    {
        if (!$this->exists($key)) {
            if (!$this->pregResolve($key)) {
                $this->autoResolve($key);
            }
        }

        if (!$this->exists($key)) {
            throw new LogicException("Class '$key' was not resolved from unknown reason");
        }

        return $this->objects[$key]->getObject($key);
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function addScalar(string $name, $value)
    {
        $this->scalars[$name] = $value;
    }

    /**
     * @param array $scalars
     */
    public function addScalarSet(array $scalars)
    {
        $this->scalars = array_merge($this->scalars, $scalars);
    }

    /**
     * @param string $key
     * @param ObjectHolderInterface $holder
     */
    public function add(string $key, ObjectHolderInterface $holder)
    {
        $this->objects[$key] = $holder;
    }

    /**
     * @param string $pregKey
     * @param ObjectHolderInterface $holder
     */
    public function addPreg(string $pregKey, ObjectHolderInterface $holder)
    {
        $this->pregObjects[$pregKey] = $holder;
    }

    /**
     * @param string $factoryKey
     * @param string $objectKey
     * @param callable $callback
     */
    public function addFactory(string $factoryKey, string $objectKey, callable $callback)
    {
        $factoryHolder = new Singleton($callback);

        $this->add($factoryKey, $factoryHolder);

        $this->add($objectKey, new Singleton(function () use ($factoryHolder, $factoryKey, $objectKey) {
            $factory = $factoryHolder->getObject($objectKey);
            if (!$factory instanceof FactoryInterface) {
                throw new LogicException("Factory $factoryKey must implement FactoryInterface");
            }

            return $factory->getSingleton();
        }));
    }

    /**
     * @return ObjectHolderInterface[]
     */
    public function getHolders()
    {
        return $this->objects;
    }

    /**
     * @return ObjectHolderInterface[]
     */
    public function getPregHolders()
    {
        return $this->pregObjects;
    }

    /**
     * @param string $classPreg
     * @return bool
     */
    protected function pregResolve(string $classPreg)
    {
        foreach ($this->pregObjects as $pregKey => $pregObject) {
            if (preg_match($pregKey, $classPreg, $matches)) {
                $className = $matches[0];
                $this->add($className, $pregObject);
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $className
     */
    protected function autoResolve(string $className)
    {
        $reflectionClass = $this->getReflectionClass($className);

        if (!$reflectionClass->isInstantiable()) {
            throw new NewInstanceException("'$className' is not an instantiable class");
        }

        $params = [];
        $reflectionConstructor = $reflectionClass->getConstructor();

        $reflectionParams = [];
        if (!is_null($reflectionConstructor)) {
            $reflectionParams = $reflectionConstructor->getParameters();
        }

        foreach ($reflectionParams as $reflectionParam) {
            $paramName = $reflectionParam->getName();
            $paramClassReflection = $reflectionParam->getClass();
            if (is_null($paramClassReflection)) {
                if (is_null($reflectionParam->getType() || !$reflectionParam->getType()->isBuiltin())) {
                    throw new AutoResolveException("Argument '\${$paramName}' of class '{$className}' cannot be resolved automatically");
                }

                if (!array_key_exists($paramName, $this->scalars)) {
                    throw new AutoResolveException("Scalar argument '\${$paramName}' of class '{$className}' has no value configured");
                }

                $params[] = $this->scalars[$paramName];
            } else {
                $this->addReflectionClass($paramClassReflection);

                try {
                    $params[] = $this->get($paramClassReflection->getName());
                } catch (NewInstanceException $e) {
                    throw new AutoResolveException("Argument '\${$paramName}' of class '{$className}' cannot be resolved automatically ({$e->getMessage()})");
                }
            }
        }

        $class = $reflectionClass->newInstanceArgs($params);
        $this->add($className, new InstantSingleton($class));
    }

    /**
     * @param string $className
     * @return ReflectionClass
     */
    protected function getReflectionClass(string $className) : ReflectionClass
    {
        if (!array_key_exists($className, $this->resolvedReflections)) {
            $this->addReflectionClass(new ReflectionClass($className));
        }

        return $this->resolvedReflections[$className];
    }

    /**
     * @param ReflectionClass $reflectionClass
     */
    protected function addReflectionClass(ReflectionClass $reflectionClass)
    {
        $this->resolvedReflections[$reflectionClass->getName()] = $reflectionClass;
    }
}
