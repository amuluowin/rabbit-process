<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/25
 * Time: 9:25
 */

namespace rabbit\process;

/**
 * Interface ProcessInterface
 * @package rabbit\process
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