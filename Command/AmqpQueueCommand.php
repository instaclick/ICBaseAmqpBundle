<?php
/**
 * @copyright 2014 Instaclick Inc.
 */

namespace IC\Bundle\Base\AmqpBundle\Command;

/**
 * Command create and delete AMQP queues.
 *
 * @author Kinn Coelho Julião <kinnj@nationalfibre.net>
 * @author Enzo Rizzo <enzor@nationalfibre.net>
 * @author Paul Munson <pmunson@nationalfibre.net>
 */
class AmqpQueueCommand extends AmqpCommand
{
    protected $commandName        = 'ic:base:amqp:queue';
    protected $commandDescription = 'Declare/Delete queue';
    protected $serviceName        = 'ic_base_amqp.service.queue_list';
}
