<?php
namespace Hooloovoo\DI\Definition;

use Hooloovoo\DI\Container\ContainerInterface;

/**
 * Interface DefinitionClassInterface
 */
interface DefinitionClassInterface
{
    /**
     * @param ContainerInterface $container
     */
    public function setUpContainer(ContainerInterface $container);
}