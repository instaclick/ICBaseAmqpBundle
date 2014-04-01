<?php
/**
 * @copyright 2014 Instaclick Inc.
 */

namespace IC\Bundle\Base\AmqpBundle\Service;

/**
 * Queue service layer
 *
 * @author Kinn Coelho Julião <kinnj@nationalfibre.net>
 */
class QueueService extends AbstractAmqpService
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        $this->serviceName = 'ic_base_amqp.service.queue_list';
    }

    /**
     * {@inheritdoc}
     */
    public function announce($name)
    {
        $this->error     = null;
        $queueService = $this->container->get($name);

        return (bool) $queueService->declareQueue();
    }
}
