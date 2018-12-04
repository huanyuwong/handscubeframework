<?php

namespace Handscube\Console\Writers;

use Handscube\Console\Interfaces\Writer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ControllerWriter implements Writer
{
    const __COMMON_CTRL_SPACE__ = "App\Controllers";
    const __TPL__ = __DIR__ . '/../Template/';

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
        $options = $this->parseOption($input, $options);
        if ($input->getOption('resource')) {
            $this->writeContentWithTemplate($input, $output, $options);
        } else {
            $contents = $this->content($options['namespace'], $options['controller']);
            $fp = $this->openFp($options['file']);
            if ($fp === -1) {
                exit($output->writeln("<error>Controller" . $options['file'] . "is exists.</error>"));
            }
            if ($this->write($fp, $contents)) {
                $output->writeln("<info>Create controller " . $options['controller'] . "successful.</info>");
            } else {
                $output->writeln("<error>Create controller" . $options['controller'] . " fail.</error>");
            }
        }

    }

    /**
     * Undocumented function
     *
     * @param array $options
     * @return array parsed Options.
     */
    protected function parseOption($input, array $options)
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
        ? $options['baseDir'] . "/controllers/$controllerName" . ".php"
        : $options['baseDir'] . "/controllers/$module/$controllerName" . ".php";
        return ['module' => $module, 'controller' => $controllerName, 'namespace' => $space, 'file' => $file];
    }

    protected function writeContentWithTemplate($input, $output, $options)
    {
        $template = file_get_contents(realpath(self::__TPL__) . '/ResourceController.tpl');
        $template = preg_replace('/{\w+?}/', $options['controller'], $template);
        if (file_put_contents($options['file'], $template) !== false) {
            $output->writeln("<info>Resource controller " . $options['controller'] . "create successful.</info>");
        } else {
            $output->writeln("<error>Resource controller " . $options['controller'] . "create errorl.</error>");
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

    public function openFp($file, $type = "w")
    {
        if (file_exists($file)) {
            return -1;
        }
        return fopen($file, $type);
    }
}
