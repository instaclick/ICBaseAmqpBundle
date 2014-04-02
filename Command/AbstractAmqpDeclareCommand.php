<?php
/**
 * @copyright 2014 Instaclick Inc.
 */

namespace IC\Bundle\Base\AmqpBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Abstract AMQP Declare command.
 *
 * @author Kinn Coelho Julião <kinnj@nationalfibre.net>
 */
abstract class AbstractAmqpDeclareCommand extends AbstractAmqpCommand
{
    /**
     * Delete the queues/exchanges listed as argument(s)
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function executeList(InputInterface $input, OutputInterface $output)
    {
        $this->service->announceList($input->getArgument('list'));

        $this->generateOutput($this->service->getErrorList(), $output);
    }

    /**
     * Delete queues/exchange
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @throws \RuntimeException
     */
    protected function executeAll(OutputInterface $output)
    {
        $list = $this->service->findAll();

        $this->service->announceList($list);

        $this->generateOutput($this->service->getErrorList(), $output);
    }
}
