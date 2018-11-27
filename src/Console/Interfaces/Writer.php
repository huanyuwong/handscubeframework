<?php

namespace Handscube\Console\Interfaces;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface Writer
{

    public function content($entitySpace, $entityName);

    public function writeContent(InputInterface $input, OutputInterface $output, array $options);
}
