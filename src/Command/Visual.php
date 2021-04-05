<?php declare(strict_types=1);

namespace PhoenixRVD\TestDrive\Command;

use PhoenixRVD\TestDrive\Driver\AbstractDriver;
use PhoenixRVD\TestDrive\Driver\DriverFactory;
use PhoenixRVD\TestDrive\Driver\Report;
use PhoenixRVD\TestDrive\Driver\TestResult;
use PhoenixRVD\TestDrive\Helper\CliHelper;
use PhoenixRVD\TestDrive\ReportRenderer\CliRenderer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Visual extends Command
{
    protected static $defaultName = 'td:visual';
    protected static $defaultDescription = 'Start visual testing in different browsers and mobile emulators.';

    protected function configure()
    {
        $this->addArgument('url', InputArgument::REQUIRED, 'Test - URI');

        $this->addOption(
            '--no-chrome',
            null,
            InputOption::VALUE_NONE,
            'No GoogleChrome'
        );

        $this->addOption(
            '--no-firefox',
            null,
            InputOption::VALUE_NONE,
            'No Firefox'
        );

        $this->addOption(
            '--no-generals',
            null,
            InputOption::VALUE_NONE,
            'No questions for general errors and warnings'
        );

        $this->addOption(
            '--no-mobile',
            null,
            InputOption::VALUE_NONE,
            'No Mobile'
        );

        $this->addOption(
            '--mobile',
            null,
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
            'List of emulated mobile devices.',
            ['Pixel 2', 'iPad', 'iPhone X']
        );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cliHelper = new CliHelper($this, $input, $output);
        $uri = $input->getArgument('url');

        $tetResults = new Report($uri);

        $skipChrome = $input->getOption('no-chrome');
        $tetResults->addSkipped('GoogleChrome', $skipChrome);

        $skipFirefox = $input->getOption('no-firefox');
        $tetResults->addSkipped('Firefox', $skipFirefox);

        $skipMobile = $input->getOption('no-mobile');
        $tetResults->addSkipped('Mobile', $skipMobile);

        $mobileDevices = $skipMobile ? [] : array_filter($input->getOption('mobile'));

        $devices = (new DriverFactory())->autoCreate(!$skipChrome, !$skipFirefox, $mobileDevices);

        foreach ($devices as $device) {
            $output->writeln(['', 'Test-Device: ' . $device->getDevice()]);
            $device->open()->get($uri);

            do {
                $result = $this->executeOne($device, $cliHelper);
                $tetResults->addDeviceTest($result);
            } while ($cliHelper->confirmRepeat());

            $device->close();
        }

        if (!$input->getOption('no-generals')) {
            $output->writeln('');
            while ('' !== ($error = $cliHelper->askForErrors())) {
                $tetResults->addGeneralError($error);
            }

            while ('' !== ($warning = $cliHelper->askForWarnings())) {
                $tetResults->addGeneralWarning($warning);
            }
        }

        $report = new CliRenderer();
        $report->render($tetResults, $input, $output);

        return self::SUCCESS;
    }

    private function executeOne(AbstractDriver $driver, CliHelper $helper): TestResult
    {
        $result = new TestResult();
        $result->setDevice($driver->getDevice());

        if (!$helper->confirmSuccess()) {
            return $result;
        }

        $errorDescription = $helper->askForErrorDescription();
        $result->setComment($errorDescription);
        $result->setPass(false);

        if ($helper->confirmScreenshot()) {
            $screenshot = $driver->takeScreenshot();
            $result->setScreenshot($screenshot);
        }

        return $result;
    }
}