<?php
/**
 * @copyright 2014 Instaclick Inc.
 */

namespace IC\Bundle\Base\AmqpBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Abstract AMQP command.
 *
 * @author Kinn Coelho Julião <kinnj@nationalfibre.net>
 */
abstract class AbstractAmqpCommand extends ContainerAwareCommand
{
    /**
     * @var \IC\Bundle\Base\AmqpBundle\Amqp\Service\ExchangeService
     */
    protected $service;

    /**
     * @var string
     */
    protected $commandName;

    /**
     * @var string
     */
    protected $commandDescription;

    /**
     * @var string
     */
    protected $serviceName;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addOption(
                'all',
                null,
                InputOption::VALUE_NONE
            )
            ->addArgument(
                $this->resourceType,
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                null,
                array()
            );
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if ( ! $this->isValid($input, $output)) {
            return;
        }

        $this->service = $this->getContainer()->get($this->serviceName);

        $output->writeln("<comment>This operation may take a while</comment>");

        $this->actionStrategy($input, $output);
    }

    /**
     * Validate the command arguments and trigger error messages
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return mixed
     */
    protected function isValid(InputInterface $input, OutputInterface $output)
    {
        if (count($input->getArgument($this->resourceType)) === 0 && $input->getOption('all') === false) {
            $output->writeln(sprintf('<error>You must provide a list of one or more %ss</error>', $this->resourceType));
            $output->writeln(sprintf('<info>Usage: %s</info>', $this->getSynopsis()));

            return false;
        }

        return true;
    }

    /**
     * Build and trigger the method depending on the options
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    protected function actionStrategy(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('all')) {
            return $this->executeAll($output);
        }

        return $this->executeList($input, $output);
    }

    /**
     * Generates output
     *
     * @param array                                             $errorList
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function generateOutput($errorList, OutputInterface $output)
    {
        foreach ($errorList as $error) {
            $output->writeln(sprintf("<error>[error]: %s</error>", $error));
        }

        $output->writeln("<info>Done</info>");
    }

    /**
     * Execute a batch operation
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    abstract protected function executeAll(OutputInterface $output);

    /**
     * Execute a batch operation for a given input
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    abstract protected function executeList(InputInterface $input, OutputInterface $output);
}
