<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/25
 * Time: 10:38
 */

namespace rabbit\process;

/**
 * Class AbstractProcess
 * @package rabbit\process
 */
class AbstractProcess implements ProcessInterface
{
    /**
     * @var bool
     */
    private $boot = false;
    /**
     * @var bool
     */
    private $co = false;
    /**
     * @var bool
     */
    private $inout = false;
    /**
     * @var int
     */
    private $pipe = 2;

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
    public function wait(): bool
    {
        return true;
    }
}