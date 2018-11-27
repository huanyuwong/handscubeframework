<?php

namespace Handscube\Console\Commands;

use Handscube\Kernel\Defer\Reactor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartWorkerCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName("start:worker")
            ->setDescription("Create a worker")
            ->setHelp("This command allows you to create a worker");
        // $this
        //     ->addArgument('worker-name', InputArgument::REQUIRED, 'The name of worker');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $reactor = new Reactor();
        if ($reactor->state === 'START') {
            $output->writeln('<info>worker start</info>');
        } else {
            $output->writeln('<error>start fail.</error>');
        }
    }

}
