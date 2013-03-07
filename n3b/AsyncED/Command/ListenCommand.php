<?php

namespace n3b\AsyncED\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

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
	 * @see Command
	 */
	protected function configure()
	{
		$this
			->setName( 'n3bAsyncED:listen' )
			->setDescription( 'Start listening events' )
			->addOption( self::OPTION_EVENT_NAME, 0, InputOption::VALUE_OPTIONAL, 'Exact event name.', null )
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

		$this->events = $this->input->getOption( self::OPTION_EVENT_NAME )
			? array( $this->input->getOption( self::OPTION_EVENT_NAME ) )
			: array( 'testQueue' ); //todo dep inj

		$this->subscribe();

		null === $this->input->getOption( self::OPTION_CONTINUOUS )
			? $this->iteration()
			: $this->infinite();
	}

	protected function subscribe()
	{

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
		$dispatcher = $this->getContainer()->get( 'event_dispatcher' );
		$builder = $this->getContainer()->get( 'n3b_queues.queue_builder' );

		foreach( $this->events as $eventName )
		{
			$queue = $builder->get( $eventName );

			if( false !== $event = $queue->dequeue() )
			{
				$this->output->writeln( sprintf( 'Catch event %s', $eventName) );
				$dispatcher->dispatch( $eventName, $event );
				$this->output->writeln( sprintf( 'Processed event %s', $eventName) );
			}
		}
	}
}
