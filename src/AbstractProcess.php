<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/25
 * Time: 10:38
 */

namespace rabbit\process;

use rabbit\contract\Arrayable;
use rabbit\server\WorkerHandlerInterface;

/**
 * Class AbstractProcess
 * @package rabbit\process
 */
abstract class AbstractProcess implements ProcessInterface, Arrayable
{
    /**
     * @var bool
     */
    protected $boot = false;
    /**
     * @var bool
     */
    protected $co = true;
    /**
     * @var bool
     */
    protected $inout = false;
    /**
     * @var int
     */
    protected $pipe = 0;
    /** @var int */
    protected $poolSize = 0;

    /**
     * @var int
     */
    protected $status = self::STATUS_STOP;

    /**
     * @var array | WorkerHandlerInterface[]
     */
    private $handlers = [];

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