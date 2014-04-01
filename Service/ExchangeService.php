<?php
/**
 * @copyright 2014 Instaclick Inc.
 */

namespace IC\Bundle\Base\AmqpBundle\Service;

/**
 * Exchange service layer
 *
 * @author Kinn Coelho Julião <kinnj@nationalfibre.net>
 */
class ExchangeService extends AbstractAmqpService
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        $this->serviceName = 'ic_base_amqp.service.exchange_list';
    }

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
