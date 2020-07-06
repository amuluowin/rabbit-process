<?php
declare(strict_types=1);

namespace Rabbit\Process;

use Rabbit\Server\BootInterface;
use Rabbit\Server\ServerHelper;

/**
 * Class BootProcess
 * @package rabbit\process
 */
class BootProcess implements BootInterface
{
    /**
     *
     */
    public function handle(): void
    {
        /** @var ProcessManager $processManager */
        $processManager = getDI('process');
        $processManager->autoStart(ServerHelper::getServer()->getSwooleServer());
    }

}