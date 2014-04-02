<?php
/**
 * @copyright 2014 Instaclick Inc.
 */

namespace IC\Bundle\Base\AmqpBundle\Tests\Service\Command;

use IC\Bundle\Base\TestBundle\Test\TestCase;
use IC\Bundle\Base\AmqpBundle\Service\Command\ExchangeService;

/**
 * Service layer to remove exchange
 *
 * @group Unit
 *
 * @author Kinn Coelho Julião <kinnj@nationalfibre.net>
 */
class ExchangeServiceTest extends TestCase
{
    /**
     * Should find all
     *
     * @param array $exchangeList
     *
     * @dataProvider exchangeListDataProvider
     */
    public function testShouldFindAll($exchangeList)
    {
        $service = new ExchangeService();

        $service->setContainer(
            $this->createContainerMock(
                $exchangeList
            )
        );

        $this->assertEquals($exchangeList, $service->findAll());
    }

    /**
     * Should announce an exchange
     *
     * @param array $exchangeName
     *
     * @dataProvider exchangeDataProvider
     */
    public function testShouldAnnounceExchange($exchangeName)
    {
        $service = new ExchangeService();

        $service->setContainer(
            $this->createContainerMock(
                $this->createAmqpExchangeMock(
                    'declareExchange',
                    $this->returnValue(true)
                )
            )
        );

        $deleted = $service->announce($exchangeName);

        $this->assertTrue($deleted);
        $this->assertCount(0, $service->getErrorList());
    }

    /**
     * Should announce an exchange list
     *
     * @param array $exchangeList
     *
     * @dataProvider exchangeListDataProvider
     */
    public function testShouldAnnounceExchangeList($exchangeList)
    {
        $service = new ExchangeService();

        $service->setContainer(
            $this->createContainerMock(
                $this->createAmqpExchangeMock(
                    'declareExchange',
                    $this->returnValue(true)
                )
            )
        );

        $service->announceList($exchangeList);

        $this->assertCount(0, $service->getErrorList());
    }

    /**
     * Should not announce an exchange
     *
     * @param array $exchangeName
     *
     * @dataProvider exchangeDataProvider
     */
    public function testShouldNotAnnounceExchange($exchangeName)
    {
        $service = new ExchangeService();

        $service->setContainer(
            $this->createContainerMock(
                $this->createAmqpExchangeMock(
                    'declareExchange',
                    $this->returnValue(false)
                )
            )
        );

        $deleted = $service->announce($exchangeName);

        $this->assertFalse($deleted);
        $this->assertCount(0, $service->getErrorList());
    }

    /**
     * Should throw an exception on announce exchange
     *
     * @param array $exchangeName
     *
     * @dataProvider exchangeDataProvider
     * @expectedException AMQPExchangeException
     */
    public function testShouldThrowExceptionOnAnnounceExchange($exchangeName)
    {
        $service = new ExchangeService();

        $service->setContainer(
            $this->createContainerMock(
                $this->createAmqpExchangeMock(
                    'declareExchange',
                    $this->throwException(new \AMQPExchangeException("PreCondition Failed"))
                )
            )
        );

        $deleted = $service->announce($exchangeName);

        $this->assertFalse($deleted);
        $this->assertContains("PreCondition Failed", $service->getErrorList());
    }

    /**
     * Should delete an exchange
     *
     * @param array $exchangeName
     *
     * @dataProvider exchangeDataProvider
     */
    public function testShouldDeleteExchange($exchangeName)
    {
        $service = new ExchangeService();

        $service->setContainer(
            $this->createContainerMock(
                $this->createAmqpExchangeMock(
                    'delete',
                    $this->returnValue(true)
                )
            )
        );

        $deleted = $service->delete($exchangeName);

        $this->assertTrue($deleted);
        $this->assertCount(0, $service->getErrorList());
    }

    /**
     * Should delete an exchange list
     *
     * @param array $exchangeList
     *
     * @dataProvider exchangeListDataProvider
     */
    public function testShouldDeleteExchangeList($exchangeList)
    {
        $service = new ExchangeService();

        $service->setContainer(
            $this->createContainerMock(
                $this->createAmqpExchangeMock(
                    'delete',
                    $this->returnValue(true)
                )
            )
        );

        $service->deleteList($exchangeList);

        $this->assertCount(0, $service->getErrorList());
    }

    /**
     * Should not delete an exchange
     *
     * @param array $exchangeName
     *
     * @dataProvider exchangeDataProvider
     */
    public function testShouldNotDeleteExchange($exchangeName)
    {
        $service = new ExchangeService();

        $service->setContainer(
            $this->createContainerMock(
                $this->createAmqpExchangeMock(
                    'delete',
                    $this->returnValue(false)
                )
            )
        );

        $deleted = $service->delete($exchangeName);

        $this->assertFalse($deleted);
        $this->assertCount(0, $service->getErrorList());
    }

    /**
     * Should throw an exception on delete exchange
     *
     * @param array $exchangeName
     *
     * @dataProvider exchangeDataProvider
     * @expectedException AMQPExchangeException
     */
    public function testShouldThrowExceptionOnDeleteExchange($exchangeName)
    {
        $service = new ExchangeService();

        $service->setContainer(
            $this->createContainerMock(
                $this->createAmqpExchangeMock(
                    'delete',
                    $this->throwException(new \AMQPExchangeException("Non Existent Queue"))
                )
            )
        );

        $deleted = $service->delete($exchangeName);

        $this->assertFalse($deleted);
        $this->assertContains("Non Existent Queue", $service->getErrorList());
    }

    /**
     * Exchange data provider
     *
     * @return array
     */
    public function exchangeDataProvider()
    {
        return array(
            array('my_fancy_exchange'),
            array('my.fancy.exchange'),
            array('another_exchange')
        );
    }

    /**
     * Exchange list data provider
     *
     * @return array
     */
    public function exchangeListDataProvider()
    {
        return array(
            array(
                array(
                    'my_fancy_exchange1',
                    'my.fancy.exchange1',
                    'another.fancy_exchange1',
                )
            ),
            array(
                array(
                    'my_fancy_exchange2',
                    'my.fancy.exchange2',
                    'another.fancy_exchange2',
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
     * Create an AmqpExchange mock
     *
     * @param string $method
     * @param mixed  $returnValue
     *
     * @return \AmqpExchange $amqpExchangeMock
     */
    private function createAmqpExchangeMock($method, $returnValue)
    {
        $amqpExchangeMock = $this->createMock('\AmqpExchange');

        $amqpExchangeMock->expects($this->at(0))
            ->method($method)
            ->will($returnValue);

        return $amqpExchangeMock;
    }
}
