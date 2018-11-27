<?php

namespace Handscube\Console\Writers;

use Handscube\Console\Interfaces\Writer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModelWriter implements Writer
{

    public function content($entitySpace, $entityName)
    {
        $contents = "<?php\n\r"
            . "namespace $entitySpace;\n\r"
            . "use Handscube\Kernel\Model;\n\r"
            . "class $entityName extends Model{\n\r\n\r\n\r"
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
            $modelName = substr($argument, $pos + 1);
        } else {
            $module = 'index';
            $modelName = $argument;
        }
        $space = strtolower($module) == 'index' ? "App\Models" : "App\\" . ucfirst($module) . "\\Controllers";
        $file = strtolower($module) == 'index'
        ? $options['baseDir'] . "/app/models/$modelName" . ".php"
        : $options['baseDir'] . "/app/models/$module/$modelName" . ".php";
        $contents = $this->content($space, $modelName);
        $fp = $this->openFp($file);
        if ($fp === -1) {
            exit($output->writeln("<error>Model $file is exists.</error>"));
        }
        if ($this->write($fp, $contents)) {
            $output->writeln("<info>Create model $modelName successful.</info>");
        } else {
            $output->writeln("<error>Create model $modelName fail.</error>");
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
