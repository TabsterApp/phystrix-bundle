<?php
/**
 * Date: 01/05/15
 * Time: 09:44
 */

namespace Odesk\Bundle\PhystrixBundle\Command;

use Odesk\Phystrix;
use Symfony\Component\DependencyInjection\Container;
use Zend\Config\Config;
use Zend\Di\LocatorInterface;

class SymfonyCommandFactory extends Phystrix\CommandFactory
{

    protected $serviceContainer;

    /**
     * @param Config $config
     * @param LocatorInterface $serviceLocator
     * @param Phystrix\CircuitBreakerFactory $circuitBreakerFactory
     * @param Phystrix\CommandMetricsFactory $commandMetricsFactory
     * @param Phystrix\RequestCache $requestCache
     * @param Phystrix\RequestLog $requestLog
     * @param Container $serviceContainer
     */
    public function __construct(
        Config $config,
        LocatorInterface $serviceLocator,
        Phystrix\CircuitBreakerFactory $circuitBreakerFactory,
        Phystrix\CommandMetricsFactory $commandMetricsFactory,
        Phystrix\RequestCache $requestCache,
        Phystrix\RequestLog $requestLog,
        Container $serviceContainer
    ) {
        $this->serviceLocator = $serviceLocator;
        $this->config = $config;
        $this->circuitBreakerFactory = $circuitBreakerFactory;
        $this->requestCache = $requestCache;
        $this->requestLog = $requestLog;
        $this->commandMetricsFactory = $commandMetricsFactory;
        $this->serviceContainer = $serviceContainer;
    }

    /**
     * Instantiates and configures a command
     *
     * @param string $class
     * @return SymfonyAbstractCommand
     */
    public function getCommand($class)
    {
        $parameters = func_get_args();
        array_shift($parameters);

        $reflection = new \ReflectionClass($class);
        /** @var SymfonyAbstractCommand $command */
        $command = empty($parameters) ?
            $reflection->newInstance() :
            $reflection->newInstanceArgs($parameters);

        $command->setCircuitBreakerFactory($this->circuitBreakerFactory);
        $command->setCommandMetricsFactory($this->commandMetricsFactory);
        $command->setServiceLocator($this->serviceLocator);
        $command->initializeConfig($this->config);
        $command->setRequestCache($this->requestCache);
        $command->setRequestLog($this->requestLog);
        $command->setServiceContainer($this->serviceContainer);

        return $command;
    }
}