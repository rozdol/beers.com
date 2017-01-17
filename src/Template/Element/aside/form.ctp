<?php
use Cake\Event\Event;

/**
 * Form event
 * @var \Cake\Event\Event
 */
$event = new Event('Element.MainSidebar.Form', $this, [
    'user' => $user
]);
$this->eventManager()->dispatch($event);
if (!empty($event->result)) {
    echo $event->result;
}
