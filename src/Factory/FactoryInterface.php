<?php
namespace Hooloovoo\DI\Factory;

/**
 * Interface FactoryInterface
 */
interface FactoryInterface
{
    /**
     * @return mixed
     */
    public function getNew();

    /**
     * @return mixed
     */
    public function getSingleton();

    /**
     * Refreshes singleton
     */
    public function refreshSingleton();
}