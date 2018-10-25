<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/25
 * Time: 9:26
 */

namespace rabbit\process;

use rabbit\core\ObjectFactory;
use Swoole\Process as SwooleProcess;

/**
 * Class Process
 * @package rabbit\process
 */
class Process
{
    /**
     * @var SwooleProcess
     */
    private $process;

    /**
     * Process constructor.
     * @param SwooleProcess $process
     */
    public function __construct(SwooleProcess $process)
    {
        $this->process = $process;
    }

    public function name(string $name): void
    {
        $this->process->name(ObjectFactory::get('appName', false, 'rabbit') . ': ' . $name);
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

    /**
     * @return SwooleProcess
     */
    public function getProcess(): SwooleProcess
    {
        return $this->process;
    }
}