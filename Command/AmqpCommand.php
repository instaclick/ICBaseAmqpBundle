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
 * Command create and delete AMQP exchanges and queues.
 *
 * @author Kinn Coelho Julião <kinnj@nationalfibre.net>
 * @author Enzo Rizzo <enzor@nationalfibre.net>
 * @author Paul Munson <pmunson@nationalfibre.net>
 */
abstract class AmqpCommand extends ContainerAwareCommand
{
    const TARGET_ALL     = "ALL";
    const TARGET_LIST    = "LIST";
    const ACTION_DECLARE = "DECLARE";
    const ACTION_DELETE  = "DELETE";

    protected $commandName        = null;
    protected $commandDescription = null;
    protected $serviceName        = null;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addOption(
                'declare',
                null,
                InputOption::VALUE_OPTIONAL,
                null,
                false
            )
            ->addOption(
                'delete',
                null,
                InputOption::VALUE_OPTIONAL,
                null,
                false
            )
            ->addOption(
                'all',
                null,
                InputOption::VALUE_OPTIONAL,
                null,
                false
            )
            ->addArgument(
                'list',
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
    private function isValid(InputInterface $input, OutputInterface $output)
    {
        if ((count($input->getArgument('list')) === 0 && $input->getOption('all') === false) ||
            ($input->getOption('declare') === false && $input->getOption('delete') === false)) {
            $output->writeln('<error>You should provide at least and exchange list</error>');
            $output->writeln('<info>Usage: '.$this->commandName.' --declare=true --all=true</info>');
            $output->writeln('<info>Usage: '.$this->commandName.' --delete=true --all=true</info>');
            $output->writeln('<info>Usage: '.$this->commandName.' --declare=true my.configured.parameter.1 my.configured.parameter.2 my.configured.parameter.N</info>');
            $output->writeln('<info>Usage: '.$this->commandName.' --delete=true my.configured.parameter.1 my.configured.parameter.2 my.configured.parameter.N</info>');

            return false;
        }

        return true;
    }

    /**
     * Build and trigger the method depending on the options: All | List / Declare | Delete
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    private function actionStrategy(InputInterface $input, OutputInterface $output)
    {
        $target = ($input->getOption('all')) ? self::TARGET_ALL : self::TARGET_LIST;
        $action = ($input->getOption('delete')) ? self::ACTION_DELETE: self::ACTION_DECLARE;
        $method = strtolower($action).ucfirst(strtolower($target));

        $this->$method($input, $output);
    }

    /**
     * Declare all the queues/exchanges present in the config file
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    private function declareAll(InputInterface $input, OutputInterface $output)
    {
        $list = $this->getTagged();

        $this->publish($list, $output);
    }

    /**
     * Delete all the queues/exchanges present in the config file
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    private function deleteAll(InputInterface $input, OutputInterface $output)
    {
        $list = $this->getTagged();

        $this->delete($list, $output);
    }

    /**
     * Declare the queues/exchanges listed as argument(s)
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    private function declareList($input, $output)
    {
        $this->publish($this->getServiceList($input->getArgument('list')), $output);
    }

    /**
     * Delete the queues/exchanges listed as argument(s)
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    private function deleteList($input, $output)
    {
        $this->delete($this->getServiceList($input->getArgument('list')), $output);
    }

    /**
     * Get the list of services available to be deleted/decleared
     *
     * @param array $list
     *
     * @return array
     */
    private function getServiceList($list)
    {
        $container   = $this->getContainer();
        $serviceList = array();

        foreach ($list as $serviceId) {
            $serviceList[$serviceId] = $container->get($serviceId);
        }

        return $serviceList;
    }

    /**
     * Delete queues/exchange
     *
     * @param array                                             $list
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @throws \RuntimeException
     */
    private function delete($list, OutputInterface $output)
    {
        $deleted = 0;

        foreach ($list as $key => $service) {
            try {
                $service->delete();
                $output->writeln(sprintf("<info>[%s [ %s ] ]</info>", $key, $service->getName()));

                $deleted++;
            } catch (\Exception $e) {
                throw new \RuntimeException(sprintf("<error>[%s [ %s ] ] %s</error>", $key, $service->getName(), $e->getMessage()), $e->getCode(), $e);
            }
        }

        $output->writeln(sprintf("<info>Total of %d deleted</info>", $deleted));
    }

    /**
     * Declare queue/exchange
     *
     * @param array                                             $list
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @throws \RuntimeException
     */
    private function publish($list, OutputInterface $output)
    {
        $declared = 0;

        foreach ($list as $key => $service) {
            try {
                $service->declare();
                $output->writeln(sprintf("<info>[%s [ %s ] ]</info>", $key, $service->getName()));

                $declared++;
            } catch (\Exception $e) {
                throw new \RuntimeException(sprintf("<error>[%s [ %s ] ] %s</error>", $key, $service->getName(), $e->getMessage()), $e->getCode(), $e);
            }
        }

        $output->writeln(sprintf("<info>Total of %d declared</info>", $declared));
    }

    /**
     * Get the service names by tag name
     *
     * @return object
     */
    protected function getTagged()
    {
        return $this->getContainer()->get($this->serviceName);
    }
}
