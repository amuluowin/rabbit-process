<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/25
 * Time: 10:38
 */

namespace rabbit\process;

use rabbit\contract\Arrayable;

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

    /**
     * @var int
     */
    protected $status = self::STATUS_STOP;

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