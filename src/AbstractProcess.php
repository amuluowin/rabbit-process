<?php
declare(strict_types=1);

namespace Rabbit\Process;


use Rabbit\Base\Contract\ArrayAble;
use Rabbit\Server\WorkerHandlerInterface;

/**
 * Class AbstractProcess
 * @package Rabbit\Process
 */
abstract class AbstractProcess implements ProcessInterface, ArrayAble
{
    /**
     * @var bool
     */
    protected bool $boot = false;
    /**
     * @var bool
     */
    protected bool $co = true;
    /**
     * @var bool
     */
    protected bool $inout = false;
    /**
     * @var int
     */
    protected int $pipe = 0;
    /** @var int */
    protected int $poolSize = 0;

    /**
     * @var string
     */
    protected string $status = self::STATUS_STOP;

    /**
     * @var array | WorkerHandlerInterface[]
     */
    private array $handlers = [];

    /**
     * AbstractProcess constructor.
     * @param array $handlers
     */
    public function __construct(array $handlers = [])
    {
        $this->handlers = $handlers;
    }

    /**
     *
     */
    public function processStart(Process $process): void
    {
        foreach ($this->handlers as $handler) {
            if ($handler instanceof WorkerHandlerInterface) {
                $handler->handle($process->getProcess()->pipe);
            }
        }
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return bool
     */
    public function getBoot(): bool
    {
        return $this->boot;
    }

    /**
     * @return bool
     */
    public function getCo(): bool
    {
        return $this->co;
    }

    /**
     * @return bool
     */
    public function getInout(): bool
    {
        return $this->inout;
    }

    /**
     * @return int
     */
    public function getPipe(): int
    {
        return $this->pipe;
    }

    /**
     * @return int
     */
    public function getPoolsize(): int
    {
        return $this->poolSize;
    }

    /**
     * @return bool
     */
    public function check(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function wait(bool $blocking = true): bool
    {
        return $blocking;
    }

    public function toArray(): array
    {
        return \get_object_vars($this);
    }
}