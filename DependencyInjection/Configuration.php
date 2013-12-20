<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\AmqpBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration builder.
 *
 * @author Guilherme Blanco <gblanco@nationalfibre.net>
 * @author John Cartwright <johnc@nationalfibre.net>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ic_base_amqp');

        $rootNode
            ->children()
            ->end()
            ->append($this->addConnectionsNode())
            ->append($this->addChannelsNode())
            ->append($this->addExchangesNode())
            ->append($this->addQueuesNode())
        ;

        return $treeBuilder;
    }

    /**
     * Build connections node configuration definition
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The connections tree builder
     */
    private function addConnectionsNode()
    {
        $builder = new TreeBuilder();
        $node    = $builder->root('connections');

        $node
            ->isRequired()
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('key')
            ->prototype('array')
                ->children()
                    ->scalarNode('host')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('login')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('password')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('port')
                        ->isRequired()
                        ->cannotBeEmpty()
                        ->defaultValue(5672)
                    ->end()
                    ->scalarNode('vhost')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->booleanNode('persistent')
                        ->defaultFalse()
                    ->end()
                    ->integerNode('read_timeout')
                        ->defaultValue(0)
                    ->end()
                    ->integerNode('write_timeout')
                        ->defaultValue(0)
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    /**
     * Build channels node configuration definition
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The buckets tree builder
     */
    private function addChannelsNode()
    {
        $builder = new TreeBuilder();
        $node    = $builder->root('channels');

        $node
            ->useAttributeAsKey('key')
            ->prototype('array')
                ->children()
                    ->scalarNode('connection')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->integerNode('prefetch_size')
                        ->defaultValue(0)
                    ->end()
                    ->integerNode('prefetch_count')
                        ->defaultValue(0)
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    /**
     * Build exchanges node configuration definition
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The buckets tree builder
     */
    private function addExchangesNode()
    {
        $builder = new TreeBuilder();
        $node    = $builder->root('exchanges');

        $node
            ->useAttributeAsKey('key')
            ->prototype('array')
                ->children()
                    ->scalarNode('name')
                        ->defaultNull()
                    ->end()
                    ->booleanNode('auto_declare')
                        ->defaultFalse()
                    ->end()
                    ->scalarNode('channel')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('type')
                        ->defaultValue(AMQP_EX_TYPE_DIRECT)
                        ->cannotBeEmpty()
                    ->end()
                    ->arrayNode('arguments')
                        ->useAttributeAsKey('key')
                        ->prototype('scalar')
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                    ->arrayNode('flags')
                        ->children()
                            ->booleanNode('durable')
                                ->defaultFalse()
                            ->end()
                            ->booleanNode('passive')
                                ->defaultFalse()
                            ->end()
                            ->booleanNode('auto_delete')
                                ->defaultFalse()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    /**
     * Build queues node configuration definition
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The buckets tree builder
     */
    private function addQueuesNode()
    {
        $builder = new TreeBuilder();
        $node    = $builder->root('queues');

        $node
            ->useAttributeAsKey('key')
            ->prototype('array')
                ->children()
                    ->scalarNode('name')
                        ->defaultNull()
                    ->end()
                    ->booleanNode('auto_declare')
                        ->defaultFalse()
                    ->end()
                    ->scalarNode('channel')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->arrayNode('flags')
                        ->children()
                            ->booleanNode('durable')
                                ->defaultFalse()
                            ->end()
                            ->booleanNode('passive')
                                ->defaultFalse()
                            ->end()
                            ->booleanNode('auto_delete')
                                ->defaultFalse()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('binding')
                        ->defaultValue(array())
                        ->prototype('array')
                            ->children()
                                ->scalarNode('exchange')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                                ->scalarNode('routing_key')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
