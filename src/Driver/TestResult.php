<?php declare(strict_types=1);

namespace PhoenixRVD\TestDrive\Driver;

use SplFileInfo;

class TestResult
{
    private string $device = '';
    private bool $pass = true;

    /**
     * @return string
     */
    public function getDevice(): string
    {
        return $this->device;
    }

    /**
     * @return bool
     */
    public function isPass(): bool
    {
        return $this->pass;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @return SplFileInfo|null
     */
    public function getScreenshot(): ?SplFileInfo
    {
        return $this->screenshot;
    }
    private string $comment = '';
    private ?SplFileInfo $screenshot = null;

    public function setScreenshot(string $filepath): TestResult
    {
        $this->screenshot = new SplFileInfo($filepath);

        return $this;
    }

    public function setComment(string $comment): TestResult
    {
        $this->comment = $comment;

        return $this;
    }

    public function setPass(bool $success): TestResult
    {
        $this->pass = $success;

        return $this;
    }

    public function setDevice(string $device): TestResult
    {
        $this->device = $device;

        return $this;
    }

    public function toArray(): array
    {
        return [
            $this->device,
            var_export($this->pass, true),
            $this->comment,
            $this->screenshot ? $this->screenshot->getFilename() : '',
        ];
    }

}