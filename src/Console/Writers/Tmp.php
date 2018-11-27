<?php

namespace Handscube\Console\CommandTmp;

class Tmp
{
    const __COMMON_CTRL_SPACE__ = "App\Controllers";

    public static function createController($module, $controllerName)
    {
        $space = strtolower($module) == 'index' ? self::__COMMON_CTRL_SPACE__ : "App\\" . ucfirst($module) . "\\Controllers";
        $contents = "<?php\n\r"
            . "namespace $space;\n\r"
            . "use Handscube\Kernel\Controller;"
            . "class $controllerName extends Controller{\n\r\n\r"
            . "}\n\r?>";
    }

    public static function createModel($module, $modelName)
    {
        $space = strtolower($module) == 'index' ? "App\Models" : "App\\" . ucfirst($module) . "\\Models";
        $contents = "<?php\n\r"
            . "namespace $space;\n\r"
            . "use Handscube\Kernel\Model;;"
            . "class $controllerName extends Model{\n\r\n\r"
            . "}\n\r?>";
    }
}
