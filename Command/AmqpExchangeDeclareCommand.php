<?php
/**
 * @copyright 2014 Instaclick Inc.
 */

namespace IC\Bundle\Base\AmqpBundle\Command;

/**
 * Declare AMQP exchanges.
 *
 * @author Kinn Coelho Julião <kinnj@nationalfibre.net>
 */
class AmqpExchangeDeclareCommand extends AbstractAmqpDeclareCommand
{
    /**
     * @var string
     */
    protected $commandName = 'ic:base:amqp:exchange:declare';

    /**
     * @var string
     */
    protected $commandDescription = 'Declare exchange';

    /**
     * @var string
     */
    protected $serviceName = 'ic_base_amqp.service.command_exchange';
}
