<?php

namespace Handscube\Console\Commands;

use Handscube\Kernel\Console\WriterCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateServerCommand extends WriterCommand
{

    protected $devHost = "http://localhost";

    protected function configure()
    {
        $this
            ->setName("serve")
            ->setDescription("Create a dev server.")
            ->setHelp("This command allows you to create a local develope server.");
        $this->configOption();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $port = $input->getOption('p');
        $configureFile = $input->getOption('c');
        $output->writeln("<info>Handscube local development server started:</info> <{$this->devHost}:{$port}>");
        if ($configureFile) {
            passthru($this->runServerWithConfigureFile($port, $configureFile));
        } else {
            passthru($this->runServer($port));
        }

    }

    protected function runServer($port)
    {
        return sprintf('php -S localhost:%s -t public/',
            $port);
    }

    protected function runServerWithConfigureFile($port, $file)
    {
        return sprintf('php -S localhost:%s -t public/ -c %s ',
            $port,
            $file);
    }

    protected function runQuote()
    {
        return [
            Process::escapeQuote((new PhpExecutableFinder)->find(false)),
            $this->host(),
            $this->port(),
            Process::escapeQuote(base_path()),
        ];
    }

    protected function configOption()
    {
        $this
            ->addOption(
                'p',
                null,
                InputOption::VALUE_REQUIRED,
                'The port you want used to start a local server.',
                8000
            );
        $this
            ->addOption(
                'c',
                null,
                InputOption::VALUE_REQUIRED,
                'The php configuration file you want to load',
                ''
            );
    }

    protected function basePath()
    {
        return realpath(APP_PATH . '/..//');
    }

}
