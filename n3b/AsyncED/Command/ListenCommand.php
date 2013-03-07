<?php

namespace n3b\AsyncED\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\Event;

class ListenCommand extends ContainerAwareCommand
{
	const OPTION_EVENT_NAME = 'event-name';
	const OPTION_CONTINUOUS = 'continuous';
	const OPTION_DELAY = 'loop-delay';

	/**
	 * @var InputInterface
	 */
	protected $input;

	/**
	 * @var OutputInterface
	 */
	protected $output;

	/**
	 * @var array
	 */
	protected $events;

	/**
	 * @var \SplQueue
	 */
	protected $eventQueue;

	/**
	 * @var \Symfony\Component\EventDispatcher\EventDispatcher
	 */
	protected $dispatcher;

	/**
	 * @see Command
	 */
	protected function configure()
	{
		$this
			->setName( 'n3bAsyncED:listen' )
			->setDescription( 'Start listening events' )
			->addOption( self::OPTION_CONTINUOUS, 0, InputOption::VALUE_OPTIONAL, 'Starts the infinite loop.', null )
			->addOption( self::OPTION_DELAY, 0, InputOption::VALUE_OPTIONAL, 'Event loop delay in ms.', 100000 )
			->setHelp(<<<EOF
The <info>swiftmailer:spool:send</info> command sends all emails from the spool.

<info>php app/console swiftmailer:spool:send --message-limit=10 --time-limit=10 --recover-timeout=900</info>

EOF
		)
		;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute( InputInterface $input, OutputInterface $output )
	{
		$this->input = $input;
		$this->output = $output;

		$this->eventQueue = $this->getContainer()->get( 'n3b_async_ed.event_queue' );

		// standart dispatcher here, dispatch events in current flow
		$this->dispatcher = $this->getContainer()->get( 'event_dispatcher' );
$this->dispatcher->dispatch('ololo');die();
		null === $this->input->getOption( self::OPTION_CONTINUOUS )
			? $this->iteration()
			: $this->infinite();
	}

	protected function infinite()
	{
		$delay = $this->input->getOption( self::OPTION_DELAY );

		while( 1 )
		{
			$this->iteration();
			usleep( $delay );
		}
	}

	protected function iteration()
	{
		if( ! ( $event = $this->eventQueue->dequeue() ) instanceof Event )
			return false;

		$eventName = $event->getName();

		$this->output->writeln( sprintf( 'Catch event <info>%s</info>', $eventName ) );
		$this->dispatcher->dispatch( $eventName, $event );
		$this->output->writeln( sprintf( 'Processed event <info>%s</info>', $eventName ) );
	}
}
