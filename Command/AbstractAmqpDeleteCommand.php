<?php
/**
 * @copyright 2014 Instaclick Inc.
 */

namespace IC\Bundle\Base\AmqpBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Abstract AMQP Delete command.
 *
 * @author Kinn Coelho Julião <kinnj@nationalfibre.net>
 */
abstract class AbstractAmqpDeleteCommand extends AbstractAmqpCommand
{

    /**
     * Delete the queues/exchanges listed as argument(s)
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function executeList(InputInterface $input, OutputInterface $output)
    {
        $this->service->deleteList($input->getArgument('list'));

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

        $this->service->deleteList($list);

        $this->generateOutput($this->service->getErrorList(), $output);
    }
}
