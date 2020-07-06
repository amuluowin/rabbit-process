<?php
declare(strict_types=1);

namespace Rabbit\Process;

use Rabbit\Base\App;
use Rabbit\Base\Helper\ExceptionHelper;
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
    private array $processes = [];
    /**
     * @var array
     */
    private array $processPool = [];

    /**
     * @var array
     */
    private array $definition = [];

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
                /** @var AbstractProcess $processObj */
                $processObj = $this->getProcessMaping($name);
                if ($processObj->getPoolsize() > 0) {
                    for ($i = 0; $i < $processObj->getPoolsize(); $i++) {
                        $swooleProcess = $this->buildProcess($name, $processObj, $i);
                        $this->processPool[$name][] = $swooleProcess;
                        $process->setStatus(ProcessInterface::STATUS_WORKING);
                        $server && $server->addProcess($swooleProcess->getProcess());
                    }
                } else {
                    $swooleProcess = $this->buildProcess($name, $processObj);
                    $this->processes[$name] = $swooleProcess;
                    $process->setStatus(ProcessInterface::STATUS_WORKING);
                    $server && $server->addProcess($swooleProcess->getProcess());
                }
            }
        }
    }

    /**
     * @param string $name
     * @param AbstractProcess $processObj
     * @return Process
     */
    private function buildProcess(string $name, AbstractProcess $processObj, int $index = null): Process
    {
        $swooleProcess = new SwooleProcess(function (SwooleProcess $swooleProcess) use ($name, $index, $processObj) {
            $process = new Process($swooleProcess);
            $this->runProcess($index !== null ? $name . '-' . $index : $name, $processObj, $process);
        }, $processObj->getInout(), $processObj->getPipe(), $processObj->getCo());
        $process = new Process($swooleProcess);
        return $process;
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

    /**
     * @param string $name
     * @return Process[]
     */
    public function getPool(string $name): array
    {
        if (!isset($this->processPool[$name])) {
            throw new Exception(sprintf('The %s process is not create, you must to create it first !', $name));
        }

        return $this->processPool[$name];
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
            throw new Exception(sprintf('The %s process is not create, you must to create it first !', $name));
        }

        return $this->processes[$name];
    }
}