<?php
/**
 * @copyright 2014 Instaclick Inc.
 */

namespace IC\Bundle\Base\AmqpBundle\Service\Command;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Amqp service interface
 *
 * @author Kinn Coelho Julião <kinnj@nationalfibre.net>
 */
interface AmqpServiceInterface
{
    /**
     * Declare a given name
     *
     * @param string $name
     *
     * @return boolean
     */
    public function announce($name);

    /**
     * Delete a given name
     *
     * @param string $name
     *
     * @return boolean
     */
    public function delete($name);

    /**
     * Announce a given list
     *
     * @param mixed $list
     */
    public function announceList($list);

    /**
     * Delete a given list
     *
     * @param mixed $list
     */
    public function deleteList($list);

    /**
     * Retrieve the configured service list
     *
     * @return mixed
     */
    public function findAll();

    /**
     * Retrieve the error list
     *
     * @return \ArrayIterator
     */
    public function getErrorList();

    /**
     * Purge the named queue
     *
     * @param mixed $name
     */
    public function purge($name);
}
