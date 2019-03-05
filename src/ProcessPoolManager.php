<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/25
 * Time: 19:19
 */

namespace rabbit\process;

use rabbit\core\Exception;

/**
 * Class ProcessPool
 * @package rabbit\process
 */
class ProcessPoolManager
{
    /**
     * @var array
     */
    private $processes = [];

    /**
     * @var array
     */
    private $definition = [];

    /**
     * @param bool $definition
     * @return array
     */
    public function getAll(bool $definition = true): array
    {
        return $definition ? $this->definition : $this->processes;
    }

    /**
     * @param string $name
     *
     * @return Process
     */
    public function create(string $name, AbstractProcessPool $processObj = null): \Swoole\Process\Pool
    {
        if (isset($this->processes[$name])) {
            return $this->processes[$name];
        }

        /** @var AbstractProcessPool $processObj */
        $processObj = $processObj ?? $this->getProcessMaping($name);
        $pool = $processObj->create();
        $this->processes[$name] = $pool;

        return $pool;
    }

    /**
     * @param string $name
     * @return AbstractProcess
     * @throws Exception
     */
    private function getProcessMaping(string $name): AbstractProcessPool
    {
        if (!isset($this->definition[$name])) {
            throw new Exception(sprintf('The %s process is not exist! ', $name));
        }

        $process = $this->definition[$name];

        return $process;
    }

    /**
     * @param string $name
     *
     * @return Process
     * @throws ProcessException
     */
    public function get(string $name): Process
    {
        if (!isset($this->processes[$name])) {
            throw new Exception(sprintf('The %s process is not create, you must to create by first !', $name));
        }

        return $this->processes[$name];
    }
}