<?php declare(strict_types=1);

namespace PhoenixRVD\TestDrive\Driver;

class DriverFactory
{
    public function autoCreate($chrome = false, $firefox = false, array $mobile = []): array
    {
        $result = [];

        if ($chrome) {
            $result[] = $this->chromeDesktop();
        }

        if ($firefox) {
            $result[] = $this->firefoxDesktop();
        }

        foreach ($mobile as $device) {
            $result[] = $this->mobile($device);
        }

        return $result;
    }

    public function chromeDesktop(): AbstractDriver
    {
        return $this->makeChrome();
    }

    private function makeChrome(): Chrome
    {
        return new Chrome();
    }

    public function firefoxDesktop(): AbstractDriver
    {
        return new Firefox();
    }

    public function mobile(string $device): AbstractDriver
    {
        return $this
            ->makeChrome()
            ->emulateMobile($device);
    }
}