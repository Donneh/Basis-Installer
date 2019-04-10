<?php
namespace BasisInstaller;

use Chumper\Zipper\Zipper;
use GuzzleHttp\ClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};
use Symfony\Component\Console\Output\OutputInterface;

class NewCommand extends Command
{

    protected $client;

    public function __construct(ClientInterface $client)
    {
        parent::__construct();

        $this->client = $client;
    }

    public function configure()
    {
        $this->setName('new')
            ->setDescription("Install Basis framework in specified folder.")
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the project.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->formatMessage("fg=yellow", "Application is installing."));

        $directory = getcwd() . '/' . $input->getArgument('name');
        $this->assertDirectoryDoesNotExist($directory, $output);

        $this->download($file = $this->makeFileName())
            ->extract($file, $directory)
            ->cleanUp($file);

        $output->writeln($this->formatMessage("info", "Application is ready."));
    }

    private function assertDirectoryDoesNotExist($directory, OutputInterface $output)
    {
        if(is_dir($directory)) {
            $output->writeln($this->formatMessage("error","Directory already Exists."));
            exit(1);
        }
    }

    private function download($file)
    {
        $response = $this->client->request("GET",'https://drive.google.com/uc?export=download&id=1jyZ7XTcxG4NAVvSo_AS13RWOH_uzkwpe')->getBody();

        file_put_contents($file, $response);

        return $this;
    }

    private function extract($file, $directory)
    {
        $archive = new \ZipArchive();
        $archive->open($file);

        $archive->extractTo($directory);

        return $this;
    }

    private function makeFileName()
    {
        return getcwd() . '/basis_' . md5(time().uniqid()) . '.zip';
    }

    private function cleanUp($file)
    {
        @chmod($file, 0777);
        @unlink($file);

        return  $this;
    }

    private function formatMessage($type, $message)
    {
        return "<{$type}>" . $message . "</{$type}>";
    }
}