<?php declare(strict_types=1);

namespace PhoenixRVD\TestDrive\Driver;

class Report
{
    /** @var TestResult[] array */
    private array $deviceTests = [];
    /** @var TestResult[] array */
    private array $generalWarnings = [];
    /** @var TestResult[] array */
    private array $generalErrors = [];
    private string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * @return TestResult[]
     */
    public function getGeneralWarnings(): array
    {
        return $this->generalWarnings;
    }

    /**
     * @return TestResult[]
     */
    public function getGeneralErrors(): array
    {
        return $this->generalErrors;
    }

    public function getDevices(): array
    {
        $list = [];

        foreach ($this->deviceTests as $result) {
            $list[] = $result;
        }

        return $list;
    }

    public function addSkipped(string $device, bool $isSkipped = true): self
    {
        if (!$isSkipped) {
            return $this;
        }

        $testResult = new TestResult();
        $testResult
            ->setPass(false)
            ->setComment('skipped')
            ->setDevice($device);

        return $this->addDeviceTest($testResult);
    }

    public function addDeviceTest(TestResult $result): self
    {
        return $this->addTestResult($this->deviceTests, $result);
    }

    private function addTestResult(array &$lis, TestResult $result): self
    {
        $lis[] = $result;

        return $this;
    }

    public function addGeneralWarning(string $comment): self
    {
        return $this->addByComment($this->generalWarnings, $comment);
    }

    private function addByComment(array &$list, string $comment): self
    {
        $testResult = (new TestResult())
            ->setDevice('general')
            ->setPass(false)
            ->setComment($comment);

        return $this->addTestResult($list, $testResult);
    }

    public function addGeneralError(string $comment): self
    {
        return $this->addByComment($this->generalErrors, $comment);
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}