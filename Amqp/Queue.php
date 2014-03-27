<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\AmqpBundle\Amqp;

/**
 * Queue.
 *
 * @author John Cartwright <johnc@nationalfibre.net>
 */
class Queue extends \AmqpQueue
{
    /**
     * @var array
     */
    private $bindingList = array();

    /**
     * {@inheritdoc}
     */
    public function consume($callback, $flags = null, $consumer_tag = null)
    {
        $this->consumeBinding();

        return parent::consume($callback, $flags, $consumer_tag);
    }

    /**
     * {@inheritdoc}
     */
    public function get($flags = null)
    {
        $this->consumeBinding();

        return parent::get($flags);
    }

    /**
     * {@inheritdoc}
     */
    public function declareQueue()
    {
        $return = parent::declareQueue();

        $this->consumeBinding();

        return $return;
    }

    /**
     * Bind the given exchange and routing key to the queue.
     *
     * @param \AmqpExchange $exchange
     * @param string        $routingKey
     */
    public function addBinding(\AmqpExchange $exchange, $routingKey)
    {
        $this->bindingList[] = array(
            'exchange'    => $exchange->getName(),
            'routing_key' => $routingKey
        );
    }

    /**
     * Apply the queued bindings.
     */
    private function consumeBinding()
    {
        if ( ! count($this->bindingList)) {
            return;
        }

        foreach ($this->bindingList as $binding) {
            $this->bind($binding['exchange'], $binding['routing_key']);
        }
    }
}
