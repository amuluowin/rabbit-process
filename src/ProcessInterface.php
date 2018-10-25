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
    /**
     * @param Process $process
     */
    public function run(Process $process):void;

    /**
     * @return bool
     */
    public function check(): bool;

    /**
     * @return bool
     */
    public function wait():bool;
}