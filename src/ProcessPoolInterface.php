<?php
declare(strict_types=1);

namespace Rabbit\Process;

/**
 * Interface ProcessPoolInterface
 * @package rabbit\process
 */
interface ProcessPoolInterface
{
    const SWOOLE_IPC_MSGQUEUE = SWOOLE_IPC_MSGQUEUE;
    const SWOOLE_IPC_SOCKET = SWOOLE_IPC_SOCKET;
    const SWOOLE_IPC_DEFAULT = 0;

    /**
     * @param \Swoole\Process\Pool $pool
     * @param int $workerId
     * @return mixed
     */
    public function run(\Swoole\Process\Pool $pool, int $workerId);

    /**
     * @param \Swoole\Process\Pool $pool
     * @param int $workerId
     * @return mixed
     */
    public function stop(\Swoole\Process\Pool $pool, int $workerId);

    /**
     * @param \Swoole\Process\Pool $pool
     * @param string $data
     * @return mixed
     */
    public function message(\Swoole\Process\Pool $pool, string $data);
}