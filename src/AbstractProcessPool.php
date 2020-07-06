<?php
declare(strict_types=1);

namespace Rabbit\Process;

/**
 * Class AbstractProcessPool
 * @package Rabbit\Process
 */
abstract class AbstractProcessPool implements ProcessPoolInterface
{
    /** @var int */
    protected int $workerNum = 1;
    /**
     * @var int
     */
    protected int $ipcType = 0;
    /**
     * @var int
     */
    protected int $msgqueueKey = 0;
    /**
     * @var array
     */
    protected array $listen = ['0..0.0.0', 9504, 2048];
    /**
     * @var bool
     */
    protected bool $running = true;

    /**
     * @return int
     */
    public function getWorkerNum(): int
    {
        return $this->workerNum;
    }

    /**
     * @return int
     */
    public function getIpcType(): int
    {
        return $this->ipcType;
    }

    /**
     * @return int
     */
    public function getMsgQueueKey(): int
    {
        return $this->msgqueueKey;
    }

    /**
     * @return array
     */
    public function getListen(): array
    {
        return $this->listen;
    }

    public function create(): \Swoole\Process\Pool
    {
        $pool = new \Swoole\Process\Pool($this->workerNum, $this->ipcType, $this->msgqueueKey);
        $pool->on('WorkerStart', [$this, 'onWorkerStart']);
        $pool->on('WorkerStop', [$processObj, 'onWorkerStop']);
        if ($this->ipcType === SWOOLE_IPC_SOCKET) {
            list($hots, $port, $backlog) = $this->listen;
            $pool->on('Message', [$this, 'onMessage']);
            $pool->listen($hots, $port, $backlog);
        }
        if (!$pool->start()) {
            throw new \RuntimeException(swoole_strerror(swoole_errno()));
        }
        return $pool;
    }

    /**
     * @param \Swoole\Process\Pool $pool
     * @param int $workerId
     */
    public function onWorkerStart(\Swoole\Process\Pool $pool, int $workerId)
    {
        \Swoole\Process::signal(SIGTERM, function ($signo) use ($workerId) {
            $this->running = false;
        });
        switch ($this->ipcType) {
            case self::SWOOLE_IPC_DEFAULT:
                rgo(function () use ($pool, $workerId) {
                    while ($this->running) {
                        $this->run($pool, $workerId);
                    }
                });
                break;
            case self::SWOOLE_IPC_SOCKET:
            case self::SWOOLE_IPC_MSGQUEUE:
                rgo(function () use ($pool, $workerId) {
                    $this->run($pool, $workerId);
                });
                break;
        }
    }
}