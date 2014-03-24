<?php
/**
 * @copyright 2014 Instaclick Inc.
 */

namespace IC\Bundle\Base\AmqpBundle\Command;

/**
 * Command create and delete AMQP exchanges.
 *
 * @author Kinn Coelho Julião <kinnj@nationalfibre.net>
 * @author Enzo Rizzo <enzor@nationalfibre.net>
 * @author Paul Munson <pmunson@nationalfibre.net>
 */
class AmqpExchangeCommand extends AmqpCommand
{
    protected $commandName        = 'ic:base:amqp:exchange';
    protected $commandDescription = 'Declare/Delete exchange';
    protected $serviceName        = 'ic_base_amqp.service.exchange_list';
}
