<?php

namespace Handscube\Console\Commands;

use Handscube\Console\Writers\ControllerWriter;
use Handscube\Kernel\Console\WriterCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateControllerCommand [c] Handscube.
 *
 */
class CreateControllerCommand extends WriterCommand
{

    protected function configure()
    {
        $this
            ->setName("create:controller")
            ->setDescription("Create a controller")
            ->setHelp("This command allows you to create a controller");
        $this
            ->addArgument('controller-name', InputArgument::REQUIRED, 'The name of controller');
        $this
            ->addOption(
                'resource',
                null,
                InputOption::VALUE_NONE,
                'create resource controller'
            );

    }

    /**
     * Excute command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->handle($input, $output, new ControllerWriter(), $this->options);
    }

}
