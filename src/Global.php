<?php

/**
 *  This Class is part of Handscube framework.
 *
 *  Class Handscube #Handscube
 *  @author J.W.
 */

use Handscube\Assistants\Arr;
use Handscube\Handscube;
use Handscube\Kernel\Route;

define("ROOT_PATH", __DIR__ . "/");
define("APP_PATH", Handscube::$appPath);
define("APP_CONFIG_PATH", Handscube::$configPath);
define("APP_VIEW_PATH", Handscube::$viewPath);

/**
 *
 * Global initialization check.
 */

// $commonConfig = Handscube::import(APP_CONFIG_PATH . "Common.php");

runWithEnvironment();

/**
 * Return enviriment config
 *
 * @return void
 */
function environment()
{
    $envFile = realpath(APP_PATH . "../.env");
    $env = file_get_contents($envFile);
    $env = preg_split("/[\r\n\v\t]+/", $env);
    $envConfig = Arr::splitWith("=", $env);
    return $envConfig;
}

function setEnv(string $key, string $value)
{
    $envFile = realpath(APP_PATH . "../.env");
    $data = "\n" . $key . '=' . $value;
    if (file_put_contents($envFile, $data, FILE_APPEND)) {
        return true;
    }
    return false;
}

/**
 * Get env key
 *
 * @param [type] $key
 * @return void
 */
function getKeyFromEnv(string $key)
{
    $env = environment();
    return isset($env[$key]) ? $env[$key] : null;
}

/**
 * Check the app envrioment.
 *
 * @return void
 */
function runWithEnvironment()
{
    if (strtolower(environment()['APP_ENV']) === "production"
        || strtolower(environment()['APP_DEBUG']) === "false") {
        ini_set("display_errors", 0);
        error_reporting(0);
    } else {
        register_shutdown_function("checkForFatal");
        set_error_handler("customErrorHandler");
        set_exception_handler("customExceptionHandler");
        ini_set("display_errors", "on");
        error_reporting(E_ALL);
    }
}

// function boot()
// {
//     global $appConfig, $databaseConfig;
//     // $appConfig = Composer::use (APP_CONFIG_PATH . "/" . "App.php", false);
//     // $databaseConfig = Composer::use (APP_CONFIG_PATH . "/Database.php", false);
// }

function ff($param, $havePre = "")
{
    if (!$havePre) {
        f($param);
        exit();
    } else {
        echo "<pre>";
        f($param);
        exit();
    }

}

function f($param)
{
    print_r($param);
    echo "\n";
}

/**
 * Get route by passing in route name.
 * e.g. [route name with no parameters] route('routeName'),
 * [route name with parameters] route('routeName',[id=1,token=123]).
 * @param [type] $routeName
 * @param [type] $routeParams
 * @return [string route(url)]
 */
function route($routeName, array $routeParams)
{
    $route = \Handscube\Foundations\Routing::getRouteByName($routeName);
    if ($routeParams) {
        foreach ($routeParams as $index => $single) {
            if (strpos($route, $index)) {
                $replacedOne = trim("{" . $index . "}");
                $replacedTwo = trim(":" . $index);
                $route = str_replace([$replacedOne, $replacedTwo], $single, $route);
            }
        }
    }
    $protocol = $_SERVER['REQUEST_SCHEME'] ?: "http";
    return $protocol . "://" . $_SERVER['HTTP_HOST'] . $route;
}
/**
 * read config file.
 * $key app.session_expire
 */

function config($key = 'app')
{
    $appConfig = require APP_CONFIG_PATH . "/App.php";
    $databaseConfig = require APP_CONFIG_PATH . "/Database.php";
    switch ($key) {
        case "app":
        case "appConfig":
            return $appConfig;
            break;
        case "db":
        case "database":
        case "databaseConfig":
            return $databaseConfig;
            break;
        default:
            return $appConfig;
            break;
    }
}

/**
 * Serialise config.
 *
 * @param string $key
 * @return void
 */
function serializeConfig(string $key)
{
    $value = config($key);
    $sValue = implode('.', $value);
    return $sValue;
}

function mapNamespace($name, $type)
{
    $prefix = unserialize(COMMON_NAMESPCE)[strtoupper($type)];
    return $prefix . ucfirst($name) . __CONTROLLER_SUFFIX__;
}

function mapCtrlNamespace($moduleName, $ctrlName)
{
    $prefix = unserialize(COMMON_NAMESPCE)[strtoupper('controller')];
    return $prefix . ucfirst(strtolower($moduleName)) . "\\" . ucfirst(strtolower($ctrlName)) . __CONTROLLER_SUFFIX__;
}

function customErrorHandler($errCode, $errMsg, $errFile, $errLine, $errContext = null)
{
    // if($errCode != E_USER_NOTICE || $errCode != E_NOTICE) {
    //     throw new ErrorException($errMsg, $errCode, NULL, $errFile, $errLine);
    // }

    if ($errCode === E_ERROR || $errCode === E_USER_ERROR) {
        throw new ErrorException($errMsg, $errCode, null, $errFile, $errLine);
    }
    if ($errCode === E_NOTICE || $errCode === E_USER_NOTICE) {
        echo "<b><p style='font-size:20px'>NOTICE:</p> </b> [$errCode] $errMsg in <b> $errFile : $errLine</b><br />\n";
        return;
    }
    if ($errCode === E_WARNING || $errCode === E_USER_WARNING) {
        echo "<p style='font-size:20px;margin:0'><b>WARNING[$errCode]:</b></p>$errMsg in <b> $errFile : $errLine<br />\n";
        return;
    }
}

/**
 * @param $e [Exception]
 * Handle the Exception and show it.
 */
function customExceptionHandler(\Throwable $e)
{

    if (strrpos(get_class($e), "\\") === false) {
        $errorType = get_class($e);
    } else {
        $pos = strrpos(get_class($e), "\\") . "\n";
        $errorType = substr(get_class($e), (int) $pos + 1);

    }
    //Handle custom User error.
    if ($errorType == "UserErrorException") {
        $errFile = $e->getErrorFile();
        $errLine = $e->getErrorLine();
    } else {
        $errFile = $e->getFile();
        $errLine = $e->getLine();
    }
    $errMsg = $e->getMessage();
    $errCode = $e->getCode();

    $exception = makeExceptionToMsg($e->getTraceAsString());
    $exception['errType'] = $errorType;
    $exception['errFile'] = $errFile;
    $exception['errLine'] = $errLine;
    $exception['errMsg'] = $errMsg;
    $exception['errCode'] = $errCode;

    require_once __DIR__ . '/Kernel/Exceptions/ExceptionPage.php';

    // echo "<h1 style='margin-bottom:0'>(⊙_⊙;)</h1><br>";
    // echo "<h2 style='margin:0'>" . $errorType . " : " . $e->getMessage() . "</h2><br/>" .
    //     "Thrown in " .
    //     "<b style='color:red'>" . $errFile . "</b>" .
    //     " : <b style='color:red'>" . $errLine . "</b><br>";
    // echo "<hr />";
    // echo "<p style='margin-bottom:0;padding:0;line-height:20px;font-size:20px'><b>Stack Trace:</b></p>";
    // echo "<b>" . resetTrace($e->getTraceAsString()) . "</b>";
    // if ($e->getCode() === 8) {
    //     return 1;
    // }

}

function resetTrace($trace)
{
    $arr = explode('#', $trace);
    return implode('<br># ', $arr);
}

function handleNotice(\Exceptions $e)
{
    return 1;
}

function makeExceptionToMsg(string $exception)
{
    preg_match_all('/\#\d\s*([^:]+)\:([^\n\r]*)/', $exception, $matches);
    return [
        'files' => $matches[1],
        'methods' => $matches[2],
    ];
}

/**
 * handle the fatal error to customErrorHandler.
 */
function checkForFatal()
{
    $error = error_get_last();
    print_r($error);
    if ($error["type"] == E_ERROR) {
        customErrorHandler($error["type"], $error["message"], $error["file"], $error["line"]);
    }

}
