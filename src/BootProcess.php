<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/25
 * Time: 14:31
 */

namespace rabbit\process;


use rabbit\App;
use rabbit\core\ObjectFactory;
use rabbit\server\BootInterface;

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
        $processManager = ObjectFactory::get('process');
        $processManager->autoStart(App::getServer());
    }

}