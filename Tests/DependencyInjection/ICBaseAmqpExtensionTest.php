<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\AmqpBundle\Tests\DependencyInjection;

use IC\Bundle\Base\TestBundle\Test\DependencyInjection\ExtensionTestCase;
use IC\Bundle\Base\AmqpBundle\DependencyInjection\ICBaseAmqpExtension;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Test for ICBaseAmqpExtension
 *
 * @group ICBaseAmqpBundle
 * @group Unit
 * @group DependencyInjection
 *
 * @author Guilherme Blanco <guilhermeblanco@hotmail.com>
 * @author John Cartwright <jcartdev@gmail.com>
 */
class ICBaseAmqpExtensionTest extends ExtensionTestCase
{
    /**
     * Test the configuration with no optional configuration values.
     *
     * @param array $configuration Configuration to apply against the extension.
     * @param array $expectation   Calculated assertion values.
     *
     * @dataProvider fullConfigurationProvider
     */
    public function testFullConfiguration($configuration, $expectation)
    {
        $loader = new ICBaseAmqpExtension();

        $this->load($loader, $configuration);

        $this->assertArrayHasKey('connections', $configuration);
        $this->assertArrayHasKey('channels', $configuration);
        $this->assertArrayHasKey('exchanges', $configuration);
        $this->assertArrayHasKey('queues', $configuration);

        foreach ($configuration['connections'] as $connectionKey => $connectionConfiguration) {
            $this->assertFullConnectionConfiguration($connectionKey, $connectionConfiguration, $expectation['connections'][$connectionKey]);
        }

        foreach ($configuration['channels'] as $channelKey => $channelConfiguration) {
            $this->assertFullChannelConfiguration($channelKey, $channelConfiguration);
        }

        foreach ($configuration['exchanges'] as $exchangeKey => $exchangeConfiguration) {
            $this->assertFullExchangeConfiguration($exchangeKey, $exchangeConfiguration, $expectation['exchanges'][$exchangeKey]);
        }

        foreach ($configuration['queues'] as $queueKey => $queueConfiguration) {
            $this->assertFullQueueConfiguration($queueKey, $queueConfiguration, $expectation['queues'][$queueKey]);
        }
    }

    /**
     * Data provider for configuration with all possible parameters.
     *
     * @return array
     */
    public function fullConfigurationProvider()
    {
        return array(
            array(
                'data' =>  array(
                    // Persistent
                    'connections' => $this->createFullConnectionConfiguration('connection', 'host', 'login', 'password', 1234, 'vhost', true, 50, 100),
                    'channels'    => $this->createFullChannelConfiguration('channel', 'connection', 50, 100),
                    // All flags on
                    'exchanges'   => $this->createFullExchangeConfiguration('exchange', 'exchangeName', 'channel', true, 'direct', true, true, true, array('foo' => 'bar')),
                    // All flags on
                    'queues'      => $this->createFullQueueConfiguration(
                        'queue',
                        'queue_name',
                        'channel',
                        true,
                        true,
                        true,
                        true,
                        array(
                            $this->createBindingConfiguration('exchange', 'routing_key'),
                            $this->createBindingConfiguration('exchange', 'routing_key2'),
                        )
                    )
                ),
                'expectation' => array(
                    'connections' => array(
                        'connection' => array(
                            'connection_method' => 'pconnect'
                        ),
                    ),
                    'exchanges' => array(
                        'exchange' => array(
                            'flag_value' => 22
                        ),
                    ),
                    'queues' => array(
                        'queue' => array(
                            'flag_value' => 22
                        ),
                    ),
                )
            ),
            array(
                'data' =>  array(
                    // Non-persistent
                    'connections' => $this->createFullConnectionConfiguration('connection', 'host', 'login', 'password', 1234, 'vhost', false, 0, 0),
                    'channels'    => $this->createFullChannelConfiguration('channel', 'connection', 50, 100),
                    // All flags off
                    'exchanges'   => $this->createFullExchangeConfiguration('exchange', 'exchangeName', 'channel', true, 'direct', false, false, false, array('foo' => 'bar')),
                    // All flags off
                    'queues'      => $this->createFullQueueConfiguration(
                        'queue',
                        'queue_name',
                        'channel',
                        true,
                        false,
                        false,
                        false,
                        array(
                            $this->createBindingConfiguration('exchange', 'routing_key'),
                            $this->createBindingConfiguration('exchange', 'routing_key2'),
                        )
                    )
                ),
                'expectation' => array(
                    'connections' => array(
                        'connection' => array(
                            'connection_method' => 'connect'
                        ),
                    ),
                    'exchanges' => array(
                        'exchange' => array(
                            'flag_value' => 0
                        ),
                    ),
                    'queues' => array(
                        'queue' => array(
                            'flag_value' => 0
                        ),
                    ),
                )
            ),
        );
    }

    /**
     * Test the configuration with optional configuration values omitted.
     *
     * @param array $configuration Configuration to apply against the extension.
     * @param array $expectation   Calculated assertion values.
     *
     * @dataProvider minimumConfigurationProvider
     */
    public function testMinimumConfiguration($configuration, $expectation)
    {
        $loader = new ICBaseAmqpExtension();

        $this->load($loader, $configuration);

        $this->assertArrayHasKey('connections', $configuration);
        $this->assertArrayHasKey('channels', $configuration);
        $this->assertArrayHasKey('exchanges', $configuration);
        $this->assertArrayHasKey('queues', $configuration);

        foreach ($configuration['connections'] as $connectionKey => $connectionConfiguration) {
            $this->assertFullConnectionConfiguration($connectionKey, $connectionConfiguration, $expectation['connections'][$connectionKey]);
        }

        foreach ($configuration['channels'] as $channelKey => $channelConfiguration) {
            $this->assertFullChannelConfiguration($channelKey, $channelConfiguration);
        }

        foreach ($configuration['exchanges'] as $exchangeKey => $exchangeConfiguration) {
            $this->assertMinimumExchangeConfiguration($exchangeKey, $exchangeConfiguration);
        }

        foreach ($configuration['queues'] as $queueKey => $queueConfiguration) {
            $this->assertMinimumQueueConfiguration($queueKey, $queueConfiguration);
        }
    }

    /**
     * Data provider for configuration with minimum possible parameters.
     *
     * @return array
     */
    public function minimumConfigurationProvider()
    {
        return array(
            array(
                'data' =>  array(
                    'connections' => $this->createFullConnectionConfiguration('connection', 'host', 'login', 'password', 1234, 'vhost', false, 50, 100),
                    'channels'    => $this->createFullChannelConfiguration('channel', 'connection', 50, 100),
                    'exchanges'   => $this->createMinimumExchangeConfiguration('exchange', 'channel', 'direct'),
                    'queues'      => $this->createMinimumQueueConfiguration('queue', 'channel')
                ),
                'expectation' => array(
                    'connections' => array(
                        'connection' => array(
                            'connection_method' => 'connect'
                        ),
                    ),
                )
            ),
        );
    }

    /**
     * Assertion for validating the connection configuration against all possible values.
     *
     * @param string $key           The connection key.
     * @param array  $configuration The connection configuration.
     * @param array  $expectation   Calculated assertion values.
     */
    private function assertFullConnectionConfiguration($key, $configuration, $expectation)
    {
        $methodCallIndex        = 0;
        $connectionDefinitionId = sprintf('ic_base_amqp.connection.%s', $key);

        $this->assertHasDefinition($connectionDefinitionId);

        $connectionDefinition = $this->container->getDefinition($connectionDefinitionId);

        $this->assertDICConstructorArguments($connectionDefinition, array($configuration));
        $this->assertDICDefinitionMethodCallAt($methodCallIndex++, $connectionDefinition, 'setReadTimeout', array($configuration['read_timeout']));
        $this->assertDICDefinitionMethodCallAt($methodCallIndex++, $connectionDefinition, 'setWriteTimeout', array($configuration['write_timeout']));
        $this->assertDICDefinitionMethodCallAt($methodCallIndex++, $connectionDefinition, $expectation['connection_method']);
    }

    /**
     * Assertion for validating the channel configuration against all possible values.
     *
     * @param string $key           The exchange key.
     * @param array  $configuration The exchange configuration.
     */
    private function assertFullChannelConfiguration($key, $configuration)
    {
        $methodCallIndex        = 0;
        $channelDefinitionId    = sprintf('ic_base_amqp.channel.%s', $key);
        $connectionDefinitionId = sprintf('ic_base_amqp.connection.%s', $configuration['connection']);

        $this->assertHasDefinition($channelDefinitionId);

        $channelDefinition = $this->container->getDefinition($channelDefinitionId);

        $this->assertDICConstructorArguments($channelDefinition, array(new Reference($connectionDefinitionId)));
        $this->assertDICDefinitionMethodCallAt($methodCallIndex++, $channelDefinition, 'setPrefetchSize', array($configuration['prefetch_size']));
        $this->assertDICDefinitionMethodCallAt($methodCallIndex++, $channelDefinition, 'setPrefetchCount', array($configuration['prefetch_count']));
    }

    /**
     * Assertion for validating the exchange configuration against all possible values.
     *
     * @param string $key           The exchange key.
     * @param array  $configuration The exchange configuration.
     * @param array  $expectation   Calculated assertion values.
     */
    private function assertFullExchangeConfiguration($key, $configuration, $expectation)
    {
        $methodCallIndex      = 0;
        $exchangeDefinitionId = sprintf('ic_base_amqp.exchange.%s', $key);
        $channelDefinitionId  = sprintf('ic_base_amqp.channel.%s', $configuration['channel']);

        $this->assertHasDefinition($exchangeDefinitionId);

        $exchangeDefinition = $this->container->getDefinition($exchangeDefinitionId);

        $this->assertDICConstructorArguments($exchangeDefinition, array(new Reference($channelDefinitionId)));
        $this->assertDICDefinitionMethodCallAt($methodCallIndex++, $exchangeDefinition, 'setName', array($configuration['name']));
        $this->assertDICDefinitionMethodCallAt($methodCallIndex++, $exchangeDefinition, 'setType', array($configuration['type']));
        $this->assertDICDefinitionMethodCallAt($methodCallIndex++, $exchangeDefinition, 'setArguments', array($configuration['arguments']));
        $this->assertDICDefinitionMethodCallAt($methodCallIndex++, $exchangeDefinition, 'setFlags', array($expectation['flag_value']));
        $this->assertDICDefinitionMethodCallAt($methodCallIndex++, $exchangeDefinition, 'declareExchange');
    }

    /**
     * Assertion for validating the exchange configuration against the minimum possible values.
     *
     * @param string $key           The exchange key.
     * @param array  $configuration The exchange configuration.
     */
    private function assertMinimumExchangeConfiguration($key, $configuration)
    {
        $methodCallIndex      = 0;
        $exchangeDefinitionId = sprintf('ic_base_amqp.exchange.%s', $key);
        $channelDefinitionId  = sprintf('ic_base_amqp.channel.%s', $configuration['channel']);

        $this->assertHasDefinition($exchangeDefinitionId);

        $exchangeDefinition = $this->container->getDefinition($exchangeDefinitionId);

        $this->assertDICConstructorArguments($exchangeDefinition, array(new Reference($channelDefinitionId)));
        $this->assertDICDefinitionMethodCallAt($methodCallIndex++, $exchangeDefinition, 'setName', array($key));
        $this->assertDICDefinitionMethodCallAt($methodCallIndex++, $exchangeDefinition, 'setType', array($configuration['type']));
        $this->assertDICDefinitionMethodCallAt($methodCallIndex++, $exchangeDefinition, 'setArguments', array($configuration['arguments']));

        $this->assertDICDefinitionMethodNotCalled($exchangeDefinition, 'setFlags');
        $this->assertDICDefinitionMethodNotCalled($exchangeDefinition, 'declareExchange');
    }

    /**
     * Assertion for validating the queue configuration against all possible values.
     *
     * @param string $key           The queue key.
     * @param array  $configuration The queue configuration.
     * @param array  $expectation   Calculated assertion values.
     */
    private function assertFullQueueConfiguration($key, $configuration, $expectation)
    {
        $methodCallIndex     = 0;
        $queueDefinitionId   = sprintf('ic_base_amqp.queue.%s', $key);
        $channelDefinitionId = sprintf('ic_base_amqp.channel.%s', $configuration['channel']);

        $this->assertHasDefinition($queueDefinitionId);

        $queueDefinition = $this->container->getDefinition($queueDefinitionId);

        $this->assertDICConstructorArguments($queueDefinition, array(new Reference($channelDefinitionId)));
        $this->assertDICDefinitionMethodCallAt($methodCallIndex++, $queueDefinition, 'setName', array($configuration['name']));
        $this->assertDICDefinitionMethodCallAt($methodCallIndex++, $queueDefinition, 'setFlags', array($expectation['flag_value']));
        $this->assertDICDefinitionMethodCallAt($methodCallIndex++, $queueDefinition, 'declareQueue');

        foreach ($configuration['binding'] as $binding) {
            $exchangeDefinitionId  = sprintf('ic_base_amqp.exchange.%s', $binding['exchange']);

            $this->assertDICDefinitionMethodCallAt($methodCallIndex++, $queueDefinition, 'addBinding', array(new Reference($exchangeDefinitionId), $binding['routing_key']));
        }
    }

    /**
     * Assertion for validating the queue configuration against the minimum possible values.
     *
     * @param string $key           The queue key.
     * @param array  $configuration The queue configuration.
     */
    private function assertMinimumQueueConfiguration($key, $configuration)
    {
        $methodCallIndex     = 0;
        $queueDefinitionId   = sprintf('ic_base_amqp.queue.%s', $key);
        $channelDefinitionId = sprintf('ic_base_amqp.channel.%s', $configuration['channel']);

        $this->assertHasDefinition($queueDefinitionId);

        $queueDefinition = $this->container->getDefinition($queueDefinitionId);

        $this->assertDICConstructorArguments($queueDefinition, array(new Reference($channelDefinitionId)));
        $this->assertDICDefinitionMethodCallAt($methodCallIndex++, $queueDefinition, 'setName', array($key));

        $this->assertDICDefinitionMethodNotCalled($queueDefinition, 'setFlags');
        $this->assertDICDefinitionMethodNotCalled($queueDefinition, 'declareQueue');
        $this->assertDICDefinitionMethodNotCalled($queueDefinition, 'addBinding');
    }

    /**
     * Create the connection configuration with all possible values.
     *
     * @param string  $key          The connection key.
     * @param string  $host         The hostname used to connect.
     * @param string  $login        The login credentials.
     * @param string  $password     The password credentials.
     * @param integer $port         The port.
     * @param string  $vhost        The virtual host.
     * @param boolean $persistent   Whether a persistent connection should be established.
     * @param integer $writeTimeout Write timeout in seconds.
     * @param integer $readTimeout  Read timeout in seconds.
     *
     * @return array
     */
    private function createFullConnectionConfiguration($key, $host, $login, $password, $port, $vhost, $persistent, $writeTimeout, $readTimeout)
    {
        return array(
            $key => array(
                'host' => $host,
                'login' => $login,
                'password' => $password,
                'port' => $port,
                'vhost' => $vhost,
                'persistent' => $persistent,
                'read_timeout' => $readTimeout,
                'write_timeout' => $writeTimeout
            )
        );
    }

    /**
     * Create the channel configuration with all possible values.
     *
     * @param string  $key           The channel key.
     * @param string  $connection    The connection key to bind to.
     * @param integer $prefetchSize  The maximum size of messages to retrieve.
     * @param integer $prefetchCount The maximum number of messages to retrieve.
     *
     * @return array
     */
    private function createFullChannelConfiguration($key, $connection, $prefetchSize, $prefetchCount)
    {
        return array(
            $key => array(
                'connection' => $connection,
                'prefetch_size' => $prefetchSize,
                'prefetch_count' => $prefetchCount,
            )
        );
    }

    /**
     * Create the exchange configuration with all possible values.
     *
     * @param string  $key          The exchange key.
     * @param string  $name         The exchange name.
     * @param string  $channel      The channel key to bind to.
     * @param boolean $autoDeclare  Whether to auto declare the exchange.
     * @param string  $type         The exchange type.
     * @param boolean $durable      Durable flag.
     * @param boolean $passive      Passive flag.
     * @param boolean $autoDelete   Auto-delete flag.
     * @param array   $argumentList A list of arguments.
     *
     * @return array
     */
    private function createFullExchangeConfiguration($key, $name, $channel, $autoDeclare, $type, $durable, $passive, $autoDelete, $argumentList)
    {
        return array(
            $key => array(
                'name' => $name,
                'auto_declare' => $autoDeclare,
                'channel' => $channel,
                'type' => $type,
                'flags' => array(
                    'durable' => $durable,
                    'passive' => $passive,
                    'auto_delete' => $autoDelete,
                ),
                'arguments' => $argumentList,
            )
        );
    }

    /**
     * Create the exchange configuration with the minimum possible values.
     *
     * @param string $key     The exchange key.
     * @param string $channel The channel key to bind to.
     * @param string $type    The exchange type.
     *
     * @return array
     */
    private function createMinimumExchangeConfiguration($key, $channel, $type)
    {
        return array(
            $key => array(
                'auto_declare' => false,
                'channel' => $channel,
                'type' => $type,
                'arguments' => array()
            )
        );
    }

    /**
     * Create the queue configuration with all possible values.
     *
     * @param string  $key         The queue key.
     * @param string  $name        The queue name.
     * @param string  $channel     The channel key to bind to.
     * @param boolean $autoDeclare Whether to auto declare the queue.
     * @param boolean $durable     Durable flag.
     * @param boolean $passive     Passive flag.
     * @param boolean $autoDelete  Auto-delete flag.
     * @param array   $bindingList A list of exchange/routing key to bind to.
     *
     * @return array
     */
    private function createFullQueueConfiguration($key, $name, $channel, $autoDeclare, $durable, $passive, $autoDelete, $bindingList)
    {
        return array(
            $key => array(
                'name' => $name,
                'auto_declare' => $autoDeclare,
                'channel' => $channel,
                'binding' => $bindingList,
                'flags' => array(
                    'durable' => $durable,
                    'passive' => $passive,
                    'auto_delete' => $autoDelete,
                ),
            )
        );
    }

    /**
     * Create the queue configuration with the minimum possible values.
     *
     * @param string $key     The queue key.
     * @param string $channel The channel key to bind to.
     *
     * @return array
     */
    private function createMinimumQueueConfiguration($key, $channel)
    {
        return array(
            $key => array(
                'auto_declare' => false,
                'channel' => $channel,
            )
        );
    }

    /**
     * Create the queue binding configuration.
     *
     * @param string $exchange   The exchange.
     * @param string $routingKey The routing key.
     *
     * @return array
     */
    private function createBindingConfiguration($exchange, $routingKey)
    {
        return array(
            'exchange' => $exchange,
            'routing_key' => $routingKey
        );
    }
}
