<?php declare(strict_types=1);

namespace PhoenixRVD\TestDrive\Driver;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\Service\DriverService;
use PhoenixRVD\TestDrive\Helper\ConfigHelper;

abstract class AbstractDriver
{
    protected RemoteWebDriver $driver;
    protected ConfigHelper $config;
    private DriverService $service;

    public function __construct()
    {
        $this->config = new ConfigHelper();
    }

    public abstract function getDevice(): string;

    public function open(): RemoteWebDriver
    {
        $bin = $this->getBinaryPath();
        $port = $this->config->get('TD_DRIVER_PORT', 4444);

        $this->service = new DriverService($bin, $port, [sprintf('--port=%d', $port)]);
        $this->service->start();

        $url = $this->getServerUrl();
        $this->driver = $this->initialize($url);

        return $this->driver;
    }

    abstract protected function getBinaryPath(): string;

    public function getServerUrl(): string
    {
        return $this->service->getURL();
    }

    abstract protected function initialize(string $serverUrl): RemoteWebDriver;

    public function close(): void
    {
        $this->driver->close();
        $this->service->stop();
    }

    public function takeScreenshot(): string
    {
        $data = [
            '{SUB_DIR}' => date('Y/m/d'),
            '{FILENAME}' => date('Ymd_His') . '.png',
            '{BASE_PATH}' => realpath(__DIR__ . '/../../'),
        ];

        $conf = $this->config->get('TD_SCREENSHOTS_PATH', '{BASE_PATH}/screenshots/{SUB_DIR}/{FILENAME}');
        $filePath = strtr($conf, $data);

        $this->driver->takeScreenshot($filePath);

        return $filePath;
    }
}