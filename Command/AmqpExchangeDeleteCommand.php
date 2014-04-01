<?php
/**
 * @copyright 2014 Instaclick Inc.
 */

namespace IC\Bundle\Base\AmqpBundle\Command;

/**
 * Delete AMQP exchanges.
 *
 * @author Kinn Coelho Julião <kinnj@nationalfibre.net>
 */
class AmqpExchangeDeleteCommand extends AbstractAmqpDeleteCommand
{
    /**
     * @var string
     */
    protected $commandName = 'ic:base:amqp:exchange:delete';

    /**
     * @var string
     */
    protected $commandDescription = 'Delete exchange';

    /**
     * @var string
     */
    protected $serviceName = 'ic_base_amqp.service.exchange';
}
