<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\AmqpBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Extension.
 *
 * @author Guilherme Blanco <gblanco@nationalfibre.net>
 * @author John Cartwright <johnc@nationalfibre.net>
 */
class ICBaseAmqpExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->initializeConnectionDefinitionList($config['connections'], $container);
        $this->initializeChannelDefinitionList($config['channels'], $container);
        $this->initializeExchangeDefinitionList($config['exchanges'], $container);
        $this->initializeQueueDefinitionList($config['queues'], $container);
    }

    /**
     * Initialize the list of connection definitions.
     *
     * @param array                                                   $connectionList
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function initializeConnectionDefinitionList(array $connectionList, ContainerBuilder $container)
    {
        $connectionClass = $container->getParameter('ic_base_amqp.class.connection');

        foreach ($connectionList as $connectionKey => $connectionConfiguration) {
            $connectionServiceId  = sprintf('ic_base_amqp.connection.%s', $connectionKey);
            $connectionDefinition = $this->createConnectionDefinition($connectionKey, $connectionClass, $connectionConfiguration);

            $container->setDefinition($connectionServiceId, $connectionDefinition);
        }
    }

    /**
     * Initialize the list of channel definitions.
     *
     * @param array                                                   $channelList
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function initializeChannelDefinitionList(array $channelList, ContainerBuilder $container)
    {
        $channelClass = $container->getParameter('ic_base_amqp.class.channel');

        foreach ($channelList as $channelKey => $channelConfiguration) {
            $channelServiceId  = sprintf('ic_base_amqp.channel.%s', $channelKey);
            $channelDefinition = $this->createChannelDefinition($channelKey, $channelClass, $channelConfiguration);

            $container->setDefinition($channelServiceId, $channelDefinition);
        }
    }

    /**
     * Initialize the list of exchange definitions.
     *
     * @param array                                                   $exchangeList Exchange list
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container    Container
     */
    private function initializeExchangeDefinitionList(array $exchangeList, ContainerBuilder $container)
    {
        $exchangeClass           = $container->getParameter('ic_base_amqp.class.exchange');
        $exchangeListClass       = $container->getParameter('ic_base_amqp.class.exchange_list');
        $exchangeListDefinition  = new Definition($exchangeListClass);
        $exchangeListServiceId   = 'ic_base_amqp.service.exchange_list';

        foreach ($exchangeList as $exchangeKey => $exchangeConfiguration) {
            $exchangeServiceId  = sprintf('ic_base_amqp.exchange.%s', $exchangeKey);
            $exchangeDefinition = $this->createExchangeDefinition($exchangeKey, $exchangeClass, $exchangeConfiguration);

            $container->setDefinition($exchangeServiceId, $exchangeDefinition);

            $exchangeListDefinition->addMethodCall(
                'set',
                array($exchangeServiceId, new Reference($exchangeServiceId))
            );
        }

        $container->setDefinition($exchangeListServiceId, $exchangeListDefinition);
    }

    /**
     * Create list of Queue definition.
     *
     * @param array                                                   $queueList
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function initializeQueueDefinitionList(array $queueList, ContainerBuilder $container)
    {
        $queueClass           = $container->getParameter('ic_base_amqp.class.queue');
        $queueListClass       = $container->getParameter('ic_base_amqp.class.queue_list');
        $queueListDefinition  = new Definition($queueListClass);
        $queueListServiceId   = 'ic_base_amqp.service.queue_list';

        foreach ($queueList as $queueKey => $queueConfiguration) {
            $queueServiceId  = sprintf('ic_base_amqp.queue.%s', $queueKey);
            $queueDefinition = $this->createQueueDefinition($queueKey, $queueClass, $queueConfiguration);

            $container->setDefinition($queueServiceId, $queueDefinition);

            $queueListDefinition->addMethodCall(
                'set',
                array($queueServiceId, new Reference($queueServiceId))
            );
        }

        $container->setDefinition($queueListServiceId, $queueListDefinition);
    }

    /**
     * Create a connection definition.
     *
     * @param string $connectionKey
     * @param string $connectionClass
     * @param array  $connectionConfiguration
     *
     * @return \Symfony\Component\DependencyInjection\Definition
     */
    private function createConnectionDefinition($connectionKey, $connectionClass, $connectionConfiguration)
    {
        $connectionDefinition = new Definition($connectionClass, array($connectionConfiguration));

        $connectionDefinition->addMethodCall('setReadTimeout', array($connectionConfiguration['read_timeout']));
        $connectionDefinition->addMethodCall('setWriteTimeout', array($connectionConfiguration['write_timeout']));

        $connectionMethod = $connectionConfiguration['persistent'] ? 'pconnect' : 'connect';

        $connectionDefinition->addMethodCall($connectionMethod);

        return $connectionDefinition;
    }

    /**
     * Create a channel definition.
     *
     * @param string $channelKey
     * @param string $channelClass
     * @param array  $channelConfiguration
     *
     * @return \Symfony\Component\DependencyInjection\Definition
     */
    private function createChannelDefinition($channelKey, $channelClass, $channelConfiguration)
    {
        $connectionServiceId = sprintf('ic_base_amqp.connection.%s', $channelConfiguration['connection']);

        $connectionReference = new Reference($connectionServiceId);
        $channelDefinition   = new Definition($channelClass, array($connectionReference));

        $channelDefinition->addMethodCall('setPrefetchSize', array($channelConfiguration['prefetch_size']));
        $channelDefinition->addMethodCall('setPrefetchCount', array($channelConfiguration['prefetch_count']));

        return $channelDefinition;
    }

    /**
     * Create a exchange definition.
     *
     * @param string $exchangeKey
     * @param string $exchangeClass
     * @param array  $exchangeConfiguration
     *
     * @return \Symfony\Component\DependencyInjection\Definition
     */
    private function createExchangeDefinition($exchangeKey, $exchangeClass, $exchangeConfiguration)
    {
        $channelServiceId = sprintf('ic_base_amqp.channel.%s', $exchangeConfiguration['channel']);

        $channelReference   = new Reference($channelServiceId);AAA
        $exchangeDefinition = new Definition($exchangeClass, array($channelReference));

        $exchangeDefinition->addMethodCall('setName', array($exchangeConfiguration['name'] ?: $exchangeKey));
        $exchangeDefinition->addMethodCall('setType', array(constant(sprintf('AMQP_EX_TYPE_%s', strtoupper($exchangeConfiguration['type'])))));
        $exchangeDefinition->addMethodCall('setArguments', array($exchangeConfiguration['arguments']));

        if (isset($exchangeConfiguration['flags'])) {
            $exchangeDefinition->addMethodCall('setFlags', array($this->getFlagValue($exchangeConfiguration['flags'])));
        }

        if ($exchangeConfiguration['auto_declare']) {
            $exchangeDefinition->addMethodCall('declareExchange');
        }

        return $exchangeDefinition;
    }

    /**
     * Create a queue definition.
     *
     * @param string $queueKey
     * @param string $queueClass
     * @param array  $queueConfiguration
     *
     * @return \Symfony\Component\DependencyInjection\Definition
     */
    private function createQueueDefinition($queueKey, $queueClass, $queueConfiguration)
    {
        $channelServiceId = sprintf('ic_base_amqp.channel.%s', $queueConfiguration['channel']);

        $channelReference = new Reference($channelServiceId);
        $queueDefinition  = new Definition($queueClass, array($channelReference));

        $queueDefinition->addMethodCall('setName', array($queueConfiguration['name'] ?: $queueKey));

        if (isset($queueConfiguration['flags'])) {
            $queueDefinition->addMethodCall('setFlags', array($this->getFlagValue($queueConfiguration['flags'])));
        }

        if ($queueConfiguration['auto_declare']) {
            $queueDefinition->addMethodCall('declareQueue');
        }

        foreach ($queueConfiguration['binding'] as $binding) {
            $channelServiceId = sprintf('ic_base_amqp.exchange.%s', $binding['exchange']);

            $exchangeReference = new Reference($channelServiceId);

            $queueDefinition->addMethodCall('addBinding', array($exchangeReference, $binding['routing_key']));
        }

        return $queueDefinition;
    }

    /**
     * Retrieve the flag value.
     *
     * @param array $flagList
     *
     * @return integer
     */
    private function getFlagValue($flagList)
    {
        $flagValue = 0;
        $flagValue |= isset($flagList['durable']) && $flagList['durable'] ? AMQP_DURABLE : 0;
        $flagValue |= isset($flagList['passive']) && $flagList['passive'] ? AMQP_PASSIVE : 0;
        $flagValue |= isset($flagList['auto_delete']) && $flagList['auto_delete'] ? AMQP_AUTODELETE : 0;

        return $flagValue;
    }
}
