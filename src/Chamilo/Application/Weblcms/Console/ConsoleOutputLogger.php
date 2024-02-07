<?php
namespace Chamilo\Application\Weblcms\Console;

use Psr\Log\LoggerInterface;
use Stringable;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleOutputLogger implements LoggerInterface
{
    protected OutputInterface $consoleOutput;

    public function __construct(OutputInterface $consoleOutput)
    {
        $this->consoleOutput = $consoleOutput;
    }

    public function alert(string|Stringable $message, array $context = []): void
    {
        $this->consoleOutput->writeln($message);
    }

    public function critical(string|Stringable $message, array $context = []): void
    {
        $this->consoleOutput->writeln($message);
    }

    public function debug(string|Stringable $message, array $context = []): void
    {
        $this->consoleOutput->writeln($message);
    }

    public function emergency(string|Stringable $message, array $context = []): void
    {
        $this->consoleOutput->writeln($message);
    }

    public function error(string|Stringable $message, array $context = []): void
    {
        $this->consoleOutput->writeln($message);
    }

    public function info(string|Stringable $message, array $context = []): void
    {
        $this->consoleOutput->writeln($message);
    }

    public function log($level, string|Stringable $message, array $context = []): void
    {
        $this->consoleOutput->writeln($message);
    }

    public function notice(string|Stringable $message, array $context = []): void
    {
        $this->consoleOutput->writeln($message);
    }

    public function warning(string|Stringable $message, array $context = []): void
    {
        $this->consoleOutput->writeln($message);
    }
}