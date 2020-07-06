<?php
declare(strict_types=1);

namespace Rabbit\Process;

/**
 * Interface ProcessInterface
 * @package Rabbit\Process
 */
interface ProcessInterface
{
    const STATUS_STOP = 'stop';
    const STATUS_WORKING = 'working';
    const STATUS_FINISH = 'finish';

    /**
     * @param Process $process
     */
    public function run(Process $process): void;

    /**
     * @return bool
     */
    public function check(): bool;

    /**
     * @return bool
     */
    public function wait(bool $blocking = true): bool;
}