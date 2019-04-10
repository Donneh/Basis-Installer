<?php
namespace BasisInstaller;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

class NewCommand extends Command
{

    public function configure()
    {
        $this->setName('new')
            ->setDescription("Install Basis framework in specified folder.")
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the project.');
    }

    public function execute()
    {

    }
}