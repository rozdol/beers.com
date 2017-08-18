<?php
namespace App\Event\Plugin\CsvMigrations\View;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Http\ServerRequest;
use Cake\ORM\Entity;
use CsvMigrations\Event\EventName;
use RolesCapabilities\CapabilityTrait;

class ViewViewTabsListener implements EventListenerInterface
{
    use CapabilityTrait;

    /**
     * Implemented Events
     * @return array
     */
    public function implementedEvents()
    {
        return [
            (string)EventName::VIEW_TABS_LIST() => [
                'callable' => 'getTabsList',
                'priority' => 20
            ]
        ];
    }

    /**
     * getTabsList method.
     *
     * Return the list of associations for the Entity as the tabs.
     *
     * @param \Cake\Event $event passed
     * @param \Cake\Http\ServerRequest $request from the view
     * @param \Cake\ORM\Entity $entity passed
     * @param array $user User info
     * @param array $options Extra options
     * @return void
     */
    public function getTabsList(Event $event, ServerRequest $request, Entity $entity, array $user, array $options)
    {
        if (empty($event->result['tabs'])) {
            return;
        }

        foreach ($event->result['tabs'] as $key => $value) {
            list($plugin, $controller) = pluginSplit($value['targetClass']);
            $url = ['plugin' => $plugin, 'controller' => $controller, 'action' => 'index'];
            if ($this->_checkAccess($url, $user)) {
                continue;
            }

            // remove tabs that user has no permission to access
            unset($event->result['tabs'][$key]);
        }
    }
}
