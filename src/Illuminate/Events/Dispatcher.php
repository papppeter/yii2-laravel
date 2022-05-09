<?php
namespace papppeter\yii2Laravel\Illuminate\Events;

use yii\base\Component;
use yii\base\Event;
use yii\base\UnknownMethodException;

/**
 *
 */
class Dispatcher extends Event {
    /**
     * Register an event listener with the dispatcher.
     *
     * @param  string|array $events
     * @param  mixed $listener
     * @return void
     */
    public function listen($events, $listener)
    {
        foreach ((array) $events as $event) {
            \Yii::$app->on($event, $listener);
        }
    }

    /**
     * Determine if a given event has listeners.
     *
     * @param  string $eventName
     * @return bool
     */
    public function hasListeners($eventName)
    {
        return \Yii::$app->hasEventHandlers($eventName);
    }

    /**
     * Register an event subscriber with the dispatcher.
     *
     * @param  object|string $subscriber
     * @return void
     */
    public function subscribe($subscriber)
    {
        throw new UnknownMethodException();
    }

    /**
     * Dispatch an event until the first non-null response is returned.
     *
     * @param  string|object $event
     * @param  mixed $payload
     * @return array|null
     */
    public function until($event, $payload = [])
    {
        throw new UnknownMethodException();
    }

    /**
     * Dispatch an event and call the listeners.
     *
     * @param  string|object $event
     * @param  mixed $payload
     * @param  bool $halt
     * @return array|null
     */
    public function dispatch($event, $payload = [], $halt = false)
    {
        if($event instanceof Event) {
            return \Yii::$app->trigger($event->name, $event);
        }

        list($event, $payload) = $this->parseEventAndPayload(
            $event, $payload
        );

        $name = $event;
        $event = new Event();
        $event->data = $payload[0];
        $event->sender = $payload[1];
        $event->handled = $halt;

        return \Yii::$app->trigger($name, $event);
    }

    /**
     * Register an event and payload to be fired later.
     *
     * @param  string $event
     * @param  array $payload
     * @return void
     */
    public function push($event, $payload = [])
    {
        throw new UnknownMethodException();
    }

    /**
     * Flush a set of pushed events.
     *
     * @param  string $event
     * @return void
     */
    public function flush($event)
    {
        throw new UnknownMethodException();
    }

    /**
     * Remove a set of listeners from the dispatcher.
     *
     * @param  string $event
     * @return void
     */
    public function forget($event)
    {
        \Yii::$app->off($event);
    }

    /**
     * Forget all of the queued listeners.
     *
     * @return void
     */
    public function forgetPushed()
    {
        throw new UnknownMethodException();
    }

    /**
     * Fire an event and call the listeners.
     *
     * @param  string|object  $event
     * @param  mixed  $payload
     * @param  bool  $halt
     * @return array|null
     */
    public function fire($event, $payload = [], $halt = false)
    {
        return $this->dispatch($event, $payload, $halt);
    }


    /**
     * Parse the given event and payload and prepare them for dispatching.
     *
     * @param  mixed  $event
     * @param  mixed  $payload
     * @return array
     */
    protected function parseEventAndPayload($event, $payload)
    {
        if (is_object($event)) {
            [$payload, $event] = [[$event], get_class($event)];
        }

        return [$event, array_wrap($payload)];
    }
}