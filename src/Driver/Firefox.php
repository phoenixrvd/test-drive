<?php declare(strict_types=1);

namespace PhoenixRVD\TestDrive\Driver;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class Firefox extends AbstractDriver
{
    public function getDevice(): string
    {
        return 'Firefox';
    }

    protected function initialize(string $serverUrl): RemoteWebDriver
    {
        $caps = DesiredCapabilities::firefox();
        $caps->setCapability('acceptSslCerts', false);

        $driver = RemoteWebDriver::create($serverUrl, $caps);

        $driver->manage()
            ->window()
            ->maximize();

        return $driver;
    }

    protected function getBinaryPath(): string
    {
        return $this->config->get('TD_DRIVER_GECKO_EXECUTABLE', 'geckodriver');
    }
}