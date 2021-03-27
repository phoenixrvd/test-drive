<?php declare(strict_types=1);

namespace PhoenixRVD\TestDrive\ReportRenderer;

use PhoenixRVD\TestDrive\Driver\Report;
use PhoenixRVD\TestDrive\Driver\TestResult;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CliRenderer
{
    public function configure(Command $command): self
    {
        return $this;
    }

    public function render(Report $testReport, InputInterface $input, OutputInterface $output): void
    {
        $url = $testReport->getUrl();

        $output->writeln(['', '', "<info>URL: $url</info>"]);

        $table = new Table($output);
        $table->setHeaders(['Device', 'Pass', 'Comment', 'Screenshot']);

        /** @var TestResult $testResult */
        foreach ($testReport->getDevices() as $testResult) {
            $table->addRow($testResult->toArray());
        }

        $table->render();

        $this->renderList('<error>General errors</error>', $testReport->getGeneralErrors(), $output);
        $this->renderList('<comment>General warnings</comment>', $testReport->getGeneralWarnings(), $output);
    }

    private function renderList(string $title, array $testResults, OutputInterface $output): void
    {
        if (empty($testResults)) {
            return;
        }

        $output->writeln(['', "$title"]);

        /** @var TestResult $testResult */
        foreach ($testResults as $testResult) {
            $output->writeln(' * ' . $testResult->getComment());
        }
    }
}