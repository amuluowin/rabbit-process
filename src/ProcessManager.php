<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/25
 * Time: 10:07
 */

namespace rabbit\process;

use rabbit\App;
use rabbit\core\Context;
use rabbit\core\Exception;
use rabbit\helper\CoroHelper;
use rabbit\helper\ExceptionHelper;
use rabbit\helper\JsonHelper;
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
    private $processes = [];

    /**
     * @var array
     */
    private $definition = [];

    /**
     * @param bool $definition
     * @return array
     */
    public function getAll(bool $definition = true): array
    {
        return $definition ? $this->definition : $this->processes;
    }

    /**
     * @throws \Exception
     */
    public function autoStart(\Swoole\Server $server = null): void
    {
        foreach ($this->definition as $name => $process) {
            /** @var AbstractProcess $process */
            if ($process->getBoot()) {
                $swooleProcess = $this->create($name, $process);
                $process->setStatus(ProcessInterface::STATUS_WORKING);
                $server && $server->addProcess($swooleProcess->getProcess());
            }
        }
    }

    /**
     * @param string $name
     *
     * @return Process
     */
    public function create(string $name, AbstractProcess $processObj = null): Process
    {
        if (isset($this->processes[$name])) {
            return $this->processes[$name];
        }

        /** @var AbstractProcess $processObj */
        $processObj = $processObj ?? $this->getProcessMaping($name);
        $swooleProcess = new SwooleProcess(function (SwooleProcess $swooleProcess) use ($name, $processObj) {
            $process = new Process($swooleProcess);
            if ($processObj->getCo()) {
                go(function () use ($name, $processObj, $process) {
                    CoroHelper::addDefer(function () {
                        Context::release();
                    });
                    $this->runProcess($name, $processObj, $process);
                });
                return;
            }
            $this->runProcess($name, $processObj, $process);
        }, $processObj->getInout(), $processObj->getPipe());
        $process = new Process($swooleProcess);
        $this->processes[$name] = $process;

        return $process;
    }

    /**
     * @param string $name
     *
     * @return Process
     * @throws ProcessException
     */
    public function get(string $name): Process
    {
        if (!isset($this->processes[$name])) {
            throw new Exception(sprintf('The %s process is not create, you must to create by first !', $name));
        }

        return $this->processes[$name];
    }

    /**
     * @param string $name
     * @return AbstractProcess
     * @throws Exception
     */
    private function getProcessMaping(string $name): AbstractProcess
    {
        if (!isset($this->definition[$name])) {
            throw new Exception(sprintf('The %s process is not exist! ', $name));
        }

        $process = $this->definition[$name];

        return $process;
    }

    /**
     * @param string $name
     * @param AbstractProcess $processObj
     * @param Process $process
     */
    private function runProcess(string $name, AbstractProcess $processObj, Process $process): void
    {
        $this->beforeProcess($name, $processObj, $process);

        if ($processObj->check()) {
            try {
                call_user_func_array([$processObj, 'processStart'], [$process]);
                call_user_func_array([$processObj, 'run'], [$process]);
            } catch (\Throwable $exception) {
                $message = ExceptionHelper::convertExceptionToArray($exception);
                App::error(JsonHelper::encode($message));
            }
        }
    }

    /**
     * @param string $processName
     * @param AbstractProcess $processObj
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    private function beforeProcess(string $processName, AbstractProcess $processObj, Process $process): void
    {
//        if ($processObj->getBoot()) {
//            ObjectFactory::reload();
//        }
        $process->name($processName);
        $this->waitChildProcess($processName, $processObj);
    }

    /**
     * @param string $name
     * @param AbstractProcess $processObj
     */
    private function waitChildProcess(string $name, AbstractProcess $processObj): void
    {
        if (($hasWait = $processObj->wait()) || $processObj->getBoot()) {
            Process::signal(SIGCHLD, function ($sig) use ($name, $processObj, $hasWait) {
                while ($ret = Process::wait(false)) {
                    if ($hasWait) {
                        $processObj->wait($ret);
                    }
                    $processObj->setStatus(ProcessInterface::STATUS_FINISH);
                    unset($this->processes[$name]);
                }
            });
        }
    }
}