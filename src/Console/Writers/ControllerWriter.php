<?php

namespace Handscube\Console\Writers;

use Handscube\Console\Interfaces\Writer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ControllerWriter implements Writer
{
    const __COMMON_CTRL_SPACE__ = "App\Controllers";

    public function content($entitySpace, $entityName)
    {
        $contents = "<?php\n\r"
            . "namespace $entitySpace;\n\r"
            . "use Handscube\Kernel\Controller;\n\r"
            . "class $entityName extends Controller{\n\r\n\r\n\r"
            . "}\n\r"
            . "?>";
        return $contents;
    }

    public function writeContent(InputInterface $input, OutputInterface $output, array $options)
    {
        $argument = $input->getArgument($options['arguments'][0]);
        if (strpos($argument, "/") !== false) {
            $pos = strpos($argument, "/");
            $module = substr($argument, 0, $pos);
            $controllerName = substr($argument, $pos + 1);
        } else {
            $module = 'index';
            $controllerName = $argument;
        }
        $space = strtolower($module) == 'index' ? self::__COMMON_CTRL_SPACE__ : "App\\" . ucfirst($module) . "\\Controllers";
        $file = strtolower($module) == 'index'
        ? $options['baseDir'] . "/app/controllers/$controllerName" . ".php"
        : $options['baseDir'] . "/app/controllers/$module/$controllerName" . ".php";
        $contents = $this->content($space, $controllerName);
        $fp = $this->openFp($file);
        if ($fp === -1) {
            exit($output->writeln("<error>Controller $file is exists.</error>"));
        }
        if ($this->write($fp, $contents)) {
            $output->writeln("<info>Create controller $controllerName successful.</info>");
        } else {
            $output->writeln("<error>Create controller $controllerName fail.</error>");
        }
    }

    public function write($fp, $contents)
    {
        if ($fp) {
            if (fwrite($fp, $contents)) {
                fclose($fp);
                return true;
            }
            fclose($fp);
            return false;
        }
    }

    public function openFp($file, $type = 'w')
    {
        if (file_exists($file)) {
            return -1;
        }
        return fopen($file, $type);
    }
}
