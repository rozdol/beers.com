<?php
namespace App\Event\View;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Network\Request;
use Cake\ORM\Entity;

class ViewMenuListener implements EventListenerInterface
{
    /**
     * Menu element name
     */
    const MENU_ELEMENT = 'Menu.menu';

    /**
     * Implemented Events
     *
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'View.View.Menu.Top' => 'getViewMenuTop'
        ];
    }

    /**
     * Method that adds elements to view View top menu.
     *
     * @param  \Cake\Event\Event     $event   Event object
     * @param  \Cake\Network\Request $request Request object
     * @param  array                 $options Entity options
     * @return string
     */
    public function getViewMenuTop(Event $event, Request $request, array $options)
    {
        $menu = [];
        $html = null;

        $urlChangelog = [
            'plugin' => $request->plugin,
            'controller' => $request->controller,
            'action' => 'changelog',
            $options['entity']->id
        ];

        $btnChangelog = $event->subject()->Html->link(
            '',
            $urlChangelog,
            ['title' => __('Changelog'), 'class' => 'btn btn-default glyphicon glyphicon-book']
        );

        $menu[] = [
            'label' => $btnChangelog,
            'url' => $urlChangelog,
            'capabilities' => 'fromUrl'
        ];

        if ($event->subject()->elementExists(static::MENU_ELEMENT)) {
            $html = $event->subject()->element(static::MENU_ELEMENT, ['menu' => $menu, 'renderAs' => 'provided']);
        } else {
            $html .= $btnChangelog;
        }

        $event->result = $html . $event->result;

        return $event->result;
    }
}
