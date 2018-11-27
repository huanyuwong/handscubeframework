<?php

namespace Handscube\Kernel\Console;

use Handscube\Console\Interfaces\Writer;
use Symfony\Component\Console\Command\Command as KernelCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WriterCommand extends KernelCommand
{

    protected $baseDir;
    protected $options = [];

    public function __construct($baseDir = null, $name = null)
    {
        $this->baseDir = $baseDir;
        $this->options['baseDir'] = $this->baseDir;
        parent::__construct($name);
    }

    public function handle(InputInterface $input, OutputInterface $output, Writer $writer, array $options, $handlerType = "writer")
    {
        if ($handlerType == "writer") {
            $this->handleToWriter($input, $output, $writer, $options);
        } else {
            $this->handleTo . ucfirst($handlerType)($input, $output, $writer, $options);
        }
    }

    public function handleToWriter(InputInterface $input, OutputInterface $output, Writer $writer, array $options)
    {
        $writer->writeContent($input, $output, $options);
    }

    public function addArgument($name, $mode = null, $description = '', $default = null)
    {
        $this->options['arguments'] = [$name];
        parent::addArgument($name, $mode, $description, $default);
    }
}
