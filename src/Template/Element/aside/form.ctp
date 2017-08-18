<?php
use App\Event\EventName;
use Cake\Event\Event;

/**
 * Form event
 * @var \Cake\Event\Event
 */
$event = new Event((string)EventName::LAYOUT_ASIDE_FORM(), $this, [
    'user' => $user
]);
$this->eventManager()->dispatch($event);
if (!empty($event->result)) {
    echo $event->result;
}
