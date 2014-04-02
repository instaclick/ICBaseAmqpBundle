<?php
/**
 * @copyright 2014 Instaclick Inc.
 */

namespace IC\Bundle\Base\AmqpBundle\Service\Command;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Abstract AMQP service layer
 *
 * @author Kinn Coelho Julião <kinnj@nationalfibre.net>
 */
abstract class AbstractAmqpService implements AmqpServiceInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var \ArrayIterator
     */
    protected $errorList;

    /**
     * @var string
     */
    protected $serviceName;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->errorList = new \ArrayIterator;
    }

    /**
     * Set the container
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($exchangeName)
    {
        $this->error     = null;
        $exchangeService = $this->container->get($exchangeName);

        return (bool) $exchangeService->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteList($list)
    {
        foreach ($list as $name) {
            $this->delete($name);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function announceList($list)
    {
        foreach ($list as $name) {
            $this->announce($name);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorList()
    {
        return $this->errorList;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        $serviceList = $this->container->get($this->serviceName);
        $nameList    = array();

        foreach ($serviceList as $serviceName => $service) {
            $nameList[] = is_string($service) ? $service : $serviceName;
        }

        return $nameList;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function announce($name);
}
