<?php

namespace Handscube\Kernel;

use Handscube\Kernel\Factories\ConnectionFactory;

/**
 * Class DBConnection [c] Handscube.
 * @author J.W. <email@email.com>
 */

class DBConnection
{

    protected static $factory;

    /**
     * Constructor.
     * Init connection factory.
     *
     * @param ConnectionFactory $factory
     */
    public function __construct(ConnectionFactory $factory)
    {
        self::$factory = self::$factory ?: $factory;
    }

    /**
     * Exporet database connection factory.
     *
     * @return void
     */
    public static function exportDB()
    {
        if (!self::$factory) {
            self::$factory = new ConnectionFactory();
        }
        return self::$factory->make();
    }
}
