<?php declare(strict_types=1);

namespace PhoenixRVD\TestDrive\Driver;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class Chrome extends AbstractDriver
{
    private array $experimentalOptions = [];

    private array $args = [
        '–disable-extensions',
        'start-maximized',
        'disable-popup-blocking',
        'test-type',
    ];

    private string $device = 'GoogleChrome';

    public function emulateMobile(string $device): self
    {
        $this->experimentalOptions['mobileEmulation'] = ['deviceName' => $device];
        $this->device = $device;
        $this->args[] = '--auto-open-devtools-for-tabs';

        return $this;
    }

    public function getDevice(): string
    {
        return $this->device;
    }

    protected function initialize(string $serverUrl): RemoteWebDriver
    {
        $options = new ChromeOptions();
        $options->addArguments($this->args);

        foreach ($this->experimentalOptions as $optionName => $optionValue) {
            $options->setExperimentalOption($optionName, $optionValue);
        }

        $caps = DesiredCapabilities::chrome();
        $caps->setCapability(ChromeOptions::CAPABILITY, $options);

        return RemoteWebDriver::create($serverUrl, $caps);
    }

    protected function getBinaryPath(): string
    {
        return $this->config->get('TD_DRIVER_CHROME_EXECUTABLE', 'chromedriver');
    }
}