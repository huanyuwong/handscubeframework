<?php

namespace Handscube\Console\Interfaces;

interface Application
{
    /**
     * Call a console command.
     *
     * @param [type] $command
     * @param array $parameters
     * @return void
     */
    public function call($command, array $parameters = []);

    /**
     * Get the output from the last command.
     *
     * @return string
     */
    public function output();
}
