<?php
namespace GDO\Dog;

use GDO\Core\Application;
use GDO\Core\GDT;

final class GDT_Timer extends GDT
{

    const INFINITE_TIMES = PHP_INT_MAX;

    /**
     * @var self[]
     */
    public static array $TIMERS = [];

    public static function addTimer(GDT_Timer $timer): void
    {
        self::$TIMERS[] = $timer;
    }

    protected function __construct()
    {
        parent::__construct();
        self::$TIMERS[] = $this;
    }

    public string $command;

    public function command(string $command): self
    {
        $this->command = $command;
        return $this;
    }

    public int $repeat = 0;
    public function repeat(int $times=self::INFINITE_TIMES): self
    {
        $this->repeat = $times;
        return $this;

    }


    public float $nextRun = 0;

    public function shouldRun(): bool
    {
        return Application::$MICROTIME >= $this->nextRun;
    }

    public int $in = 0;

    public function now(): self
    {
        return $this->in(0);
    }

    public function in(float $seconds): self
    {
        $this->in = $seconds;
        return $this;
    }

    public function every(float $seconds): self
    {
        return $this->repeat()->in($seconds);
    }

}
