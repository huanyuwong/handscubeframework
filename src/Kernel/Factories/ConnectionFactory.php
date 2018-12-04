<?php

namespace Handscube\Kernel\Factories;

use Handscube\Abstracts\Features\SimulateContainer;
use Handscube\Traits\AppAccesserTrait;
use Illuminate\Database\Connectors\ConnectionFactory as KernelConnection;

/**
 * Class ConnectionFactory [c] Handscube.
 * Based on Illumiate\Databases.
 * @author J.W. <email@email.com>
 */
class ConnectionFactory extends KernelConnection
{
    use AppAccesserTrait;

    public function __construct(SimulateContainer $container = null)
    {
        $this->container = $container ?: $this->app;
    }

    public function make(array $config = [], $dbName = 'mysql')
    {
        $dbName = $dbName ?: 'mysql';
        $config = $config ?: config('database')[$dbName];
        return $this->createSingleConnection($config);
    }
}
