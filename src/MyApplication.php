<?php

/**
 * @copyright 2025 Onetree. All rights reserved.
 * @author    Juanca <juancarlosc@onetree.com>
 */

namespace Juanca\RoboTaskUtility;

use Composer\Autoload\ClassLoader;
use Consolidation\AnnotatedCommand\CommandFileDiscovery;
use Consolidation\Config\ConfigInterface;
use Consolidation\Config\Loader\ConfigProcessor;
use Consolidation\Config\Loader\YamlConfigLoader;
use League\Container\ContainerAwareTrait;
use League\Container\DefinitionContainerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Robo\Application;
use Robo\Common\ConfigAwareTrait;
use Robo\Config\Config;
use Robo\Robo;
use Robo\Runner;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @SuppressWarnings("PHPMD.LongVariable")
 * @SuppressWarnings("PHPMD.CouplingBetweenObjects")
 * @SuppressWarnings("PHPMD.StaticAccess")
 */
class MyApplication
{
    use ConfigAwareTrait;
    use ContainerAwareTrait;

    public const APPLICATION_NAME = 'Juanca Tools Application';
    public const REPOSITORY = 'jcconde/robo-task-utility';
    /**
     * @var array<int, string>
     */
    private array $defaultCommandClasses = [];
    private Runner $runner;
    private Application $application;

    /**
     * @param ClassLoader $classLoader
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct(
        ClassLoader $classLoader
    ) {
        // create config
        $this->config = $this->createConfiguration();

        // create application
        $this->application = $this->createApplication();
        $this->application->setAutoExit(false);

        // create container
        $this->container = $this->createContainer(
            $this->application,
            $this->config,
            $classLoader
        );

        // Create and initialize runner.
        $this->runner = new Runner();
        $this->runner->setSelfUpdateRepository(self::REPOSITORY);
        $this->runner->setContainer($this->container);
    }

    /**
     * @return ConfigInterface
     */
    private function createConfiguration(): ConfigInterface
    {
        $this->config = new Config();
        $loader = new YamlConfigLoader();
        $processor = new ConfigProcessor();
        $processor->extend($loader->load(__DIR__ . '/etc/config.yml'));
        $this->config->import($processor->export());

        return $this->config;
    }

    /**
     * @return Application
     */
    private function createApplication(): Application
    {
        return new Application(self::APPLICATION_NAME, (string)$this->config->get('project/version'));
    }

    /**
     * @param Application $application
     * @param ConfigInterface $config
     * @param ClassLoader $classLoader
     * @return DefinitionContainerInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    private function createContainer(
        Application $application,
        ConfigInterface $config,
        ClassLoader $classLoader
    ): DefinitionContainerInterface {
        /** @var DefinitionContainerInterface $container */
        $container = Robo::createContainer($application, $config, $classLoader);
        $container->get('commandFactory')->setIncludeAllPublicMethods(false);
        $container->add('filesystem', Filesystem::class);
        $container->add('logger', $this->getLogger());

        return $container;
    }

    /**
     * @return LoggerInterface
     */
    private function getLogger(): LoggerInterface
    {
        $logDir = realpath(__DIR__ . '/../var/log');
        if ($logDir === false) {
            $logDir = __DIR__ . '/../var/log';
            if (!is_dir($logDir)) {
                mkdir($logDir, 0777, true);
            }
        }
        $logPath = $logDir . '/system.log';
        if (!file_exists($logPath)) {
            touch($logPath);
        }

        $logger = new Logger('robo');
        $logger->pushHandler(new StreamHandler($logPath, Level::Debug));

        return $logger;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function run(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $this->prepareApplication();
        return $this->runner->run($input, $output);
    }

    /**
     * @return void
     */
    private function prepareApplication(): void
    {
        $commandClasses = $this->getDiscoverCommandClasses();
        $commandClasses = array_merge($this->defaultCommandClasses, $commandClasses);

        $this->runner->registerCommandClasses($this->application, $commandClasses);
    }

    /**
     * @return array<string, string>
     */
    private function getDiscoverCommandClasses(): array
    {
        $discovery = new CommandFileDiscovery();
        $discovery->setSearchPattern('*Command.php');
        $commandsPath = __DIR__ . '/Commands';
        return $discovery->discover($commandsPath, '\Juanca\RoboTaskUtility\Commands');
    }
}
