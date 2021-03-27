<?php declare(strict_types=1);

namespace PhoenixRVD\TestDrive\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class Automatic extends Command
{
    protected static $defaultName = 'td:automatic';
    protected static $defaultDescription = 'Execute commands in automatic mode. See example/automatic.yam for format description.';

    protected function configure()
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Configuration file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filePath = $input->getArgument('file');
        $data = Yaml::parseFile($filePath)['td'];

        $app = $this->getApplication();

        foreach ($data as $command) {
            $commandName = $command['command'];
            $args = $this->extractInputArgs($command);
            $command = $app->find($commandName);
            $output->writeln(['', '---------------------', "Command: $commandName"]);
            $output->writeln(
                ['Arguments: ' . var_export($args, true)],
                OutputInterface::VERBOSITY_VERY_VERBOSE
            );
            $command->run(new ArrayInput($args), $output);
        }

        return Command::SUCCESS;
    }

    private function extractInputArgs(array $command): array
    {
        $args = [];

        if (!empty($command['args'])) {
            $args = array_merge($args, $command['args']);
        }

        if (!empty($command['options'])) {
            $args = array_merge($args, $command['options']);
        }

        return $args;
    }
}