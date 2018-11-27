<?php

namespace Handscube\Console\Commands;

use Handscube\Console\Writers\ModelWriter;
use Handscube\Kernel\Console\WriterCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateModelCommand extends WriterCommand
{

    protected function configure()
    {
        $this
            ->setName("create:model")
            ->setDescription("Create a model")
            ->setHelp("This command allows you to create a model");
        $this
            ->addArgument('model-name', InputArgument::REQUIRED, 'The name of model');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->handle($input, $output, new ModelWriter(), $this->options);
    }

}
