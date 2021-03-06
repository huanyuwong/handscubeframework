<?php

/**
 * This Class is part of Handscube framework.
 * Class Handscube #Handscube
 *
 * @author J.W.
 */
namespace Handscube;

use App\Kernel\App;

class Handscube
{

    const NAME = "HANDSCUBE";
    const VERSION = "v1.0.0";

    public static $appPath;
    public static $configPath;
    public static $viewPath;

    public static $isRun = false;
    public static $isMakeApp = false;

    public static $app;

    public function __construct()
    {

    }

    /**
     * bootstrap function.
     */
    public static function __bootstrap()
    {
        self::import(__DIR__ . "/Global.php", false);
    }

    /**
     * framework initialization such as setting the file directiry.
     */
    public static function __frameworkInit(string $path)
    {
        self::$isRun = true;
        date_default_timezone_set('Asia/Shanghai');
        self::$appPath = realpath($path) . "/";
        self::$configPath = realpath(self::$appPath . "../configs");
        self::$viewPath = realpath(self::$appPath . "../") . "/resources/views";
    }

    /**
     * Start the application.
     *
     * @param [string] $path [Application path]
     */
    public static function startApplication(string $path)
    {

        if (self::isMake()) {
            return self::$app;
        }
        self::$app = new App($path) ?: new Application($path);
        return self::$app;
    }

    /**
     * Run application and do some initialization.
     */
    public static function make(string $path)
    {

        $path = $path ? $path : '';
        $app = self::startApplication($path);
        self::$isMakeApp = true;
        return $app;
    }

    /**
     * Run Handscube framework.
     *
     * @return [Application] $app;
     */
    public static function run(string $appPath)
    {
        self::__frameworkInit($appPath);
        self::__bootstrap(); //load some global functions or tools.
        return self::make($appPath);
    }

    public static function isMake()
    {
        return self::$isMakeApp;
    }

    public static function import(string $path, bool $shouldReturn = true)
    {
        if ($shouldReturn) {
            return require_once $path;
        }
        require_once $path;
    }

    /**
     * return current Handscube master version.
     */
    public function getVersion()
    {
        return self::VERSION;
    }

    public function __destruct()
    {
        echo "Framework __desctruct\n";
    }

}
