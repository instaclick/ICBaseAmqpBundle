<?php
/**
 * @copyright 2014 Instaclick Inc.
 */

namespace IC\Bundle\Base\AmqpBundle\Service\Command;

/**
 * Queue service layer
 *
 * @author Kinn Coelho Julião <kinnj@nationalfibre.net>
 */
class QueueService extends AbstractAmqpService
{
    /**
     * @var string
     */
    protected $serviceName = 'ic_base_amqp.service.queue_list';

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
