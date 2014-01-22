<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Base\AmqpBundle\Amqp;

/**
 * Queue.
 *
 * @author John Cartwright <jcartdev@gmail.com>
 */
class Queue extends \AmqpQueue
{
    /**
     * Bind the given exchange and routing key to the queue.
     *
     * @param \AmqpExchange $exchange
     * @param string        $routingKey
     */
    public function addBinding(\AmqpExchange $exchange, $routingKey)
    {
        $this->bind($exchange->getName(), $routingKey);
    }
}
