<?php
declare(strict_types=1);

namespace Rabbit\Process;

use Swoole\Process as SwooleProcess;

/**
 * Class Process
 * @package Rabbit\Process
 */
class Process
{
    /**
     * @var SwooleProcess
     */
    private $process;
    /** @var string */
    private $name;

    /**
     * Process constructor.
     * @param SwooleProcess $process
     */
    public function __construct(SwooleProcess $process)
    {
        $this->process = $process;
    }

    /**
     * @param int $pid
     * @param int $signo
     * @return bool
     */
    public static function kill(int $pid, int $signo = SIGTERM): bool
    {
        return SwooleProcess::kill($pid, $signo);
    }

    /**
     * @param bool $blocking
     * @return mixed
     */
    public static function wait(bool $blocking = true)
    {
        return SwooleProcess::wait($blocking);
    }

    /**
     * @param bool $nochdir
     * @param bool $noclose
     */
    public static function daemon(bool $nochdir = false, bool $noclose = false): bool
    {
        return SwooleProcess::daemon($nochdir, $noclose);
    }

    /**
     * @param int $signo
     * @param callable $callback
     * @return bool
     */
    public static function signal(int $signo, callable $callback): bool
    {
        return SwooleProcess::signal($signo, $callback);
    }

    /**
     * @param int $intervalUsec
     * @param int $type
     * @return bool
     */
    public static function alarm(int $intervalUsec, int $type = 0): bool
    {
        return SwooleProcess::alarm($intervalUsec, $type);
    }

    /**
     * @param array $cpuSet
     * @return bool
     */
    public static function setaffinity(array $cpuSet): bool
    {
        return SwooleProcess::setaffinity($cpuSet);
    }

    public function name(string $name): void
    {
        $this->name = $name;
        $this->process->name(getDI('appName', false, 'rabbit') . ': ' . $name);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return SwooleProcess
     */
    public function getProcess(): SwooleProcess
    {
        return $this->process;
    }
}