<?php

/**
 *  This Class is part of Handscube framework.
 *
 *     Class Handscube #Handscube
 *  @Author HuanYu.Wong
 * "workbench.editor.enablePreview": false,
 */

use Handscube\Assistants\Arr;
use Handscube\Handscube;

define("ROOT_PATH", __DIR__ . "/");

define("APP_PATH", Handscube::$appPath);
define("APP_CONFIG_PATH", Handscube::$configPath);

$commonConfig = [];

/**
 *
 * Global initialization check.
 */

// $commonConfig = Handscube::import(APP_CONFIG_PATH . "Common.php");
$commonConfig = [];
runWithEnvironment();

/***
 *
 * Global Functions
 */
function environment()
{
    $envFile = realpath(APP_PATH . "../.env");
    $env = file_get_contents($envFile);
    $env = preg_split("/[\r\n\v\t]+/", $env);
    $envConfig = Arr::splitWith("=", $env);
    return $envConfig;
}

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

function initGlobal()
{

}

function ff($param, $havePre = "")
{
    if (!$havePre) {
        print_r($param);
        f();
    } else {
        echo "<pre>";
        print_r($param);
        f();
    }

}

function f()
{
    echo "\n";
    echo exit();
}
/**
 * read config file.
 */

function config(string $key)
{
    global $commonConfig;
    $tempConfig = $commonConfig;
    if (isset($key) && !empty($key)) {
        if (strpos($key, ".") !== false) {
            $target = explode(".", $key);
            foreach ($target as $k => $v) {
                $tempConfig = $tempConfig[$v];
            }
            return $tempConfig;
        } else {
            return $commonConfig[$key];
        }
    }
    return 0;
}

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
    echo "Global::customErrorHandler.\n";

    if ($errCode === E_ERROR || $errCode === E_USER_ERROR) {
        throw new ErrorException($errMsg, $errCode, null, $errFile, $errLine);
    }
    if ($errCode === E_NOTICE || $errCode === E_USER_NOTICE) {
        echo "<b><p style='font-size:24px'>NOTICE:</p> </b> [$errCode] $errMsg in <b> $errFile : $errLine</b><br />\n";
        return;
    }
    if ($errCode === E_WARNING || $errCode === E_USER_WARNING) {
        echo "<b><p style='font-size:24px'>WARNING:</p>[$errCode] $errMsg in <b> $errFile : $errLine</b><br />\n";
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
        $errorType = substr(get_class($e), $pos + 1);

    }
    //Handle custom User error.
    if ($errorType == "UserErrorException") {
        $errFile = $e->getErrorFile();
        $errLine = $e->getErrorLine();
    } else {
        $errFile = $e->getFile();
        $errLine = $e->getLine();
    }

    echo "<h1 style='margin-bottom:0'>(⊙_⊙;)</h1><br>";
    echo "<h2 style='margin:0'>" . $errorType . " : " . $e->getMessage() . "</h2><br/>" .
        "Thrown in " .
        "<b style='color:red'>" . $errFile . "</b>" .
        " : <b style='color:red'>" . $errLine . "</b><br>";
    echo "<hr />";
    echo "<p style='margin-bottom:0;padding:0;line-height:20px;font-size:20px'><b>Stack Trace:</b></p>";
    echo "<b>" . resetTrace($e->getTraceAsString()) . "</b>";
    if ($e->getCode() === 8) {
        return 1;
    }

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
