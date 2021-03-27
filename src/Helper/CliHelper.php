<?php declare(strict_types=1);

namespace PhoenixRVD\TestDrive\Helper;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class CliHelper
{
    private Command $command;
    private InputInterface $input;
    private OutputInterface $output;

    public function __construct(Command $command, InputInterface $input, OutputInterface $output)
    {
        $this->command = $command;
        $this->input = $input;
        $this->output = $output;
    }

    public function askForErrorDescription(): string
    {
        return $this->input('Describe the Error');
    }

    private function ask(Question $question)
    {
        return $this
            ->command
            ->getHelper('question')
            ->ask($this->input, $this->output, $question);
    }

    public function confirmSuccess(): bool
    {
        return $this->confirm('Does it contain errors?');
    }

    private function confirm(string $question, bool $default = false): bool
    {
        $question = new ConfirmationQuestion("$question (y/n) [n]: ", $default, '/^(y|j)/i');

        return $this->ask($question);
    }

    public function confirmRepeat(): bool
    {
        return $this->confirm('Repeat this case?');
    }

    public function confirmScreenshot(): bool
    {
        return $this->confirm('Should a screen sheet be included?');
    }

    public function askForErrors(): string
    {
        return $this->input('Add general error [click enter to skip it]');
    }

    public function askForWarnings(): string
    {
        return $this->input('Add general warning [click enter to skip it]');
    }

    private function input(string $label): string
    {
        $question = new Question($label . ': ', '');

        return $this->ask($question);
    }
}