<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/25
 * Time: 10:07
 */

namespace rabbit\process;

use rabbit\core\Exception;
use rabbit\core\ObjectFactory;
use Swoole\Process as SwooleProcess;

/**
 * Class ProcessManager
 * @package rabbit\process
 */
class ProcessManager
{
    /**
     * @var array
     */
    private static $processes = [];

    /**
     * @param string $name
     *
     * @return Process
     */
    public static function create(string $name): Process
    {
        if (isset(self::$processes[$name])) {
            return self::$processes[$name];
        }

        /** @var AbstractProcess $processObj */
        $processObj = self::getProcessMaping($name);
        $swooleProcess = new SwooleProcess(function (SwooleProcess $swooleProcess) use ($name, $processObj) {
            $process = new Process($swooleProcess);
            if ($co) {
                go(function () use ($name, $processObj, $process) {
                    self::runProcess($name, $processObj, $process);
                });
                return;
            }
            self::runProcess($name, $processObj, $process);
        }, $processObj->getInout(), $processObj->getPipe());
        $process = new Process($swooleProcess);
        self::$processes[$name] = $process;

        return $process;
    }

    /**
     * @param string $name
     *
     * @return Process
     * @throws ProcessException
     */
    public static function get(string $name): Process
    {
        if (!isset(self::$processes[$name])) {
            throw new ProcessException(sprintf('The %s process is not create, you must to create by first !', $name));
        }

        return self::$processes[$name];
    }

    /**
     * @param string $name
     * @return AbstractProcess
     * @throws Exception
     */
    private static function getProcessMaping(string $name): AbstractProcess
    {
        $collector = ObjectFactory::get('process');
        if (!isset($collector[$name])) {
            throw new Exception(sprintf('The %s process is not exist! ', $name));
        }

        $process = $collector[$name];

        return $process;
    }

    /**
     * @param string $name
     * @param Process $process
     * @param bool $boot
     * @throws \Exception
     */
    private static function runProcess(string $name, AbstractProcess $processObj, Process $process): void
    {
        self::beforeProcess($name, $processObj);
        if ($processObject->check()) {
            call_user_func_array([$processObj, 'run'], [$process]);
        }
    }

    /**
     * After process
     *
     * @param string $processName
     * @param bool $boot
     */
    private static function beforeProcess(string $processName, AbstractProcess $processObj): void
    {
        if ($processObj->getBoot()) {
            ObjectFactory::reload();
        }

        self::waitChildProcess($processName, $processObj);
    }

    /**
     * Wait child process
     */
    private static function waitChildProcess(string $name, AbstractProcess $processObj): void
    {
        if (($hasWait = method_exists($processObj, 'wait')) || $processObj->getBoot()) {
            Process::signal(SIGCHLD, function ($sig) use ($name, $processObj, $hasWait) {
                while ($ret = Process::wait(false)) {
                    if ($hasWait) {
                        $processObj->wait($ret);
                    }

                    unset(self::$processes[$name]);
                }
            });
        }
    }
}