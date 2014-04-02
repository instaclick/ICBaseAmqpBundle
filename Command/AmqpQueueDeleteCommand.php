<?php
/**
 * @copyright 2014 Instaclick Inc.
 */

namespace IC\Bundle\Base\AmqpBundle\Command;

/**
 * Delete AMQP queue.
 *
 * @author Kinn Coelho Julião <kinnj@nationalfibre.net>
 */
class AmqpQueueDeleteCommand extends AbstractAmqpDeleteCommand
{
    /**
     * @var string
     */
    protected $commandName = 'ic:base:amqp:queue:delete';

    /**
     * @var string
     */
    protected $commandDescription = 'Delete queue';

    /**
     * @var string
     */
    protected $serviceName = 'ic_base_amqp.service.command_queue';
}
