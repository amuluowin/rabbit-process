<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/25
 * Time: 19:27
 */

namespace rabbit\process;

/**
 * Interface ProcessPoolInterface
 * @package rabbit\process
 */
interface ProcessPoolInterface
{
    const SWOOLE_IPC_MSGQUEUE = SWOOLE_IPC_MSGQUEUE;
    const SWOOLE_IPC_SOCKET = SWOOLE_IPC_SOCKET;
    const SWOOLE_IPC_DEFAULT = 0;

    public function run(\Swoole\Process\Pool $pool, int $workerId);

    public function stop(\Swoole\Process\Pool $pool, int $workerId);

    public function message(\Swoole\Process\Pool $pool, string $data);
}