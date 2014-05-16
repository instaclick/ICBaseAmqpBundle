<?php
/**
 * @copyright 2014 Instaclick Inc.
 */

namespace IC\Bundle\Base\AmqpBundle\Command;

/**
 * Declare AMQP queue.
 *
 * @author Kinn Coelho Julião <kinnj@nationalfibre.net>
 */
class AmqpQueueDeclareCommand extends AbstractAmqpDeclareCommand
{
    /**
     * @var string
     */
    protected $commandName = 'ic:base:amqp:queue:declare';

    /**
     * @var string
     */
    protected $commandDescription = 'Declare queue';

    /**
     * @var string
     */
    protected $resourceType = 'queue';

    /**
     * @var string
     */
    protected $serviceName = 'ic_base_amqp.service.command_queue';
}
