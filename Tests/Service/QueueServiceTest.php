<?php
/**
 * @copyright 2014 Instaclick Inc.
 */

namespace IC\Bundle\Base\AmqpBundle\Tests\Service;

use IC\Bundle\Base\TestBundle\Test\TestCase;
use IC\Bundle\Base\AmqpBundle\Service\QueueService;

/**
 * Service layer to remove queue
 *
 * @group Unit
 *
 * @author Kinn Coelho Julião <kinnj@nationalfibre.net>
 */
class QueueServiceTest extends TestCase
{
    /**
     * Should find all
     *
     * @param array $queueList
     *
     * @dataProvider queueListDataProvider
     */
    public function testShouldFindAll($queueList)
    {
        $service = new QueueService;
        $service->setContainer(
            $this->createContainerMock(
                $queueList
            )
        );

        $this->assertEquals($queueList, $service->findAll());
    }

    /**
     * Should announce an queue
     *
     * @param array $queueName
     *
     * @dataProvider queueDataProvider
     */
    public function testShouldAnnounceQueue($queueName)
    {
        $service = new QueueService;
        $service->setContainer(
            $this->createContainerMock(
                $this->createAmqpQueueMock(
                    'declareQueue',
                    $this->returnValue(true)
                )
            )
        );

        $deleted = $service->announce($queueName);

        $this->assertTrue($deleted);
        $this->assertCount(0, $service->getErrorList());
    }

    /**
     * Should announce an queue list
     *
     * @param array $queueList
     *
     * @dataProvider queueListDataProvider
     */
    public function testShouldAnnounceQueueList($queueList)
    {
        $service = new QueueService;
        $service->setContainer(
            $this->createContainerMock(
                $this->createAmqpQueueMock(
                    'declareQueue',
                    $this->returnValue(true)
                )
            )
        );

        $service->announceList($queueList);

        $this->assertCount(0, $service->getErrorList());
    }

    /**
     * Should not announce an queue
     *
     * @param array $queueName
     *
     * @dataProvider queueDataProvider
     */
    public function testShouldNotAnnounceQueue($queueName)
    {
        $service = new QueueService;
        $service->setContainer(
            $this->createContainerMock(
                $this->createAmqpQueueMock(
                    'declareQueue',
                    $this->returnValue(false)
                )
            )
        );

        $deleted = $service->announce($queueName);

        $this->assertFalse($deleted);
        $this->assertCount(0, $service->getErrorList());
    }

    /**
     * Should throw an exception on announce queue
     *
     * @param array $queueName
     *
     * @dataProvider queueDataProvider
     * @expectedException AMQPQueueException
     */
    public function testShouldThrowExceptionOnAnnounceQueue($queueName)
    {
        $service = new QueueService;
        $service->setContainer(
            $this->createContainerMock(
                $this->createAmqpQueueMock(
                    'declareQueue',
                    $this->throwException(new \AMQPQueueException("PreCondition Failed"))
                )
            )
        );

        $deleted = $service->announce($queueName);

        $this->assertFalse($deleted);
        $this->assertContains("PreCondition Failed", $service->getErrorList());
    }

    /**
     * Should delete an queue
     *
     * @param array $queueName
     *
     * @dataProvider queueDataProvider
     */
    public function testShouldDeleteQueue($queueName)
    {
        $service = new QueueService;
        $service->setContainer(
            $this->createContainerMock(
                $this->createAmqpQueueMock(
                    'delete',
                    $this->returnValue(true)
                )
            )
        );

        $deleted = $service->delete($queueName);

        $this->assertTrue($deleted);
        $this->assertCount(0, $service->getErrorList());
    }

    /**
     * Should delete an queue list
     *
     * @param array $queueList
     *
     * @dataProvider queueListDataProvider
     */
    public function testShouldDeleteQueueList($queueList)
    {
        $service = new QueueService;
        $service->setContainer(
            $this->createContainerMock(
                $this->createAmqpQueueMock(
                    'delete',
                    $this->returnValue(true)
                )
            )
        );

        $service->deleteList($queueList);

        $this->assertCount(0, $service->getErrorList());
    }

    /**
     * Should not delete an queue
     *
     * @param array $queueName
     *
     * @dataProvider queueDataProvider
     */
    public function testShouldNotDeleteQueue($queueName)
    {
        $service = new QueueService;
        $service->setContainer(
            $this->createContainerMock(
                $this->createAmqpQueueMock(
                    'delete',
                    $this->returnValue(false)
                )
            )
        );

        $deleted = $service->delete($queueName);

        $this->assertFalse($deleted);
        $this->assertCount(0, $service->getErrorList());
    }

    /**
     * Should throw an exception on delete queue
     *
     * @param array $queueName
     *
     * @dataProvider queueDataProvider
     * @expectedException AMQPQueueException
     */
    public function testShouldThrowExceptionOnDeleteQueue($queueName)
    {
        $service = new QueueService;
        $service->setContainer(
            $this->createContainerMock(
                $this->createAmqpQueueMock(
                    'delete',
                    $this->throwException(new \AMQPQueueException("Non Existent Queue"))
                )
            )
        );

        $deleted = $service->delete($queueName);

        $this->assertFalse($deleted);
        $this->assertContains("Non Existent Queue", $service->getErrorList());
    }

    /**
     * Queue data provider
     *
     * @return array
     */
    public function queueDataProvider()
    {
        return array(
            array('my_fancy_queue'),
            array('my.fancy.queue'),
            array('another_queue')
        );
    }

    /**
     * Queue list data provider
     *
     * @return array
     */
    public function queueListDataProvider()
    {
        return array(
            array(
                array(
                    'my_fancy_queue1',
                    'my.fancy.queue1',
                    'another.fancy_queue1',
                )
            ),
            array(
                array(
                    'my_fancy_queue2',
                    'my.fancy.queue2',
                    'another.fancy_queue2',
                )
            )
        );
    }

    /**
     * Create a Container mock
     *
     * @param mixed $returnMock
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface $containerMock
     */
    private function createContainerMock($returnMock)
    {
        $containerMock = $this->createMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $containerMock->expects($this->atLeastOnce())
            ->method('get')
            ->will($this->returnValue($returnMock));

        return $containerMock;
    }

    /**
     * Create an AmqpQueue mock
     *
     * @param string $method
     * @param bool   $returnValue
     *
     * @return \AmqpQueue $amqpQueueMock
     */
    private function createAmqpQueueMock($method, $returnValue)
    {
        $amqpQueueMock = $this->createMock('\AmqpQueue');
        $amqpQueueMock->expects($this->at(0))
            ->method($method)
            ->will($returnValue);

        return $amqpQueueMock;
    }
}
