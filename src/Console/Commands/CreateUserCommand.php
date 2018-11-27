<?php

namespace Handscube\Console\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\WriterCommand\WriterCommand;

class CreateUserCommand extends WriterCommand
{
    protected function configure()
    {
        $this
            ->setName('controller')
            ->setDescription('Creates a new user.')
            ->setHelp('This command allows you to create a user...')
        ;
        $this
            ->addArgument('controllername', InputArgument::REQUIRED, 'The name of controller');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Controller name ' . $input->getArgument('controllername'));
    }
}
