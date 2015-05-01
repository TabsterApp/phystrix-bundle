<?php
/**
 * Date: 01/05/15
 * Time: 10:27
 */

namespace Odesk\Bundle\PhystrixBundle\Command;

use Odesk\Phystrix\AbstractCommand;
use Symfony\Component\DependencyInjection\Container;

abstract class SymfonyAbstractCommand extends AbstractCommand
{
    protected $serviceContainer;

    public function setServiceContainer(Container $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
    }
}