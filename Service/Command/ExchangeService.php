<?php
/**
 * @copyright 2014 Instaclick Inc.
 */

namespace IC\Bundle\Base\AmqpBundle\Service\Command;

/**
 * Exchange service layer
 *
 * @author Kinn Coelho Julião <kinnj@nationalfibre.net>
 */
class ExchangeService extends AbstractAmqpService
{
    /**
     * @var string
     */
    protected $serviceName = 'ic_base_amqp.service.exchange_list';

    /**
     * {@inheritdoc}
     */
    public function announce($name)
    {
        $this->error     = null;
        $exchangeService = $this->container->get($name);

        return (bool) $exchangeService->declareExchange();
    }
}
