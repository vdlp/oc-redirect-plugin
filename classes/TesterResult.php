<?php

declare(strict_types=1);

namespace Vdlp\Redirect\Classes;

final class TesterResult
{
    private int $duration;

    public function __construct(
        private bool $passed,
        private string $message
    ) {
        $this->duration = 0;
    }

    public function isPassed(): bool
    {
        return $this->passed;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setDuration(int $duration): TesterResult
    {
        $this->duration = $duration;

        return $this;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function getStatusCssClass(): string
    {
        return $this->passed ? 'passed' : 'failed';
    }
}
