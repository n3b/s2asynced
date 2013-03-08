# Asynchronous Event Dispatcher for Symfony2

based on async queues https://github.com/n3b/s2queues

this usefull to dispatch the events from your frontend to backend daemons

n3bQueuesBundle is required

usage:

add subscribers by tagging your services

services.yml
```yml
services:
  some_service:
    class:         MyService
    tags:
      -  { name: n3b_async_ed.event_subscriber }
```

```php

class MyService implement Symfony\Component\EventDispatcher\EventSubscriberInterface
{
  public function ololoMethod( Symfony\Component\EventDispatcher\Event $event )
  {
    // do something
    die( 'ololo!' );
  }

  static public function getSubscribedEvents()
  {
    return array(
      'ololo' => 'ololoMethod',
    );
  }
}
```



ok. now, if you want to dispatch event to backend, you must use async event dispatcher
```$this->getContainer()->get( 'n3b_async_ed.event_dispatcher' )->dispatch( 'ololo' );```

and then just run backend handler

```bash
./app/console n3bAsyncED:listen
```

note, that n3b_async_ed.event_subscriber tag add subscribes to standart event dispatcher
so, if you want to dispatch event in current flow, just do
```$this->getContainer()->get( 'event_dispatcher' )->dispatch( 'ololo' );```
