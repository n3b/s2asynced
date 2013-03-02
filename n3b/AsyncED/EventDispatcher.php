<?php

namespace n3b\AsyncED;

use Symfony\Component\EventDispatcher as Source;

class EventDispatcher extends Source\EventDispatcher
{
	private $queue;

	public function __construct( \SplQueue $queue )
	{
		$this->queue = $queue;
	}

	public function dispatch($eventName, Source\Event $event = null)
	{
		if (null === $event) {
			$event = new Event();
		}

		$event->setName($eventName);

		$this->queue && $this->queue->enqueue($event);

		return $event;
	}
}
