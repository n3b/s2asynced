<?php

namespace n3b\AsyncED;

use Symfony\Component\EventDispatcher\EventDispatcher as BaseDispatcher,
	Symfony\Component\EventDispatcher\Event;

class EventDispatcher extends BaseDispatcher
{
	private $queue;

	public function __construct( \SplQueue $queue )
	{
		$this->queue = $queue;
	}

	public function dispatch( $eventName, Event $event = null )
	{
		null === $event && $event = new Event();

		$event->setName( $eventName );

		$this->queue && $this->queue->enqueue( $event );

		return $event;
	}
}
