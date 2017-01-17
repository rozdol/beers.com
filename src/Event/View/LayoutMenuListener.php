<?php
namespace App\Event\View;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Network\Exception\ForbiddenException;
use Cake\ORM\TableRegistry;
use Search\Controller\Traits\SearchableTrait;

class LayoutMenuListener implements EventListenerInterface
{
    use SearchableTrait;

    const SEARCH_FORM_ELEMENT = 'search-form';

    /**
     * Implemented Events
     *
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Element.MainSidebar.Form' => 'getSearchForm'
        ];
    }

    /**
     * Add search form if current model is searchable and user has search access to it.
     *
     * @param Cake\Event\Event $event Event object
     * @param array $user User info
     * @return void
     */
    public function getSearchForm(Event $event, array $user)
    {
        if (!$event->subject()->elementExists(static::SEARCH_FORM_ELEMENT)) {
            return;
        }

        $tableName = $event->subject()->request->controller;
        if ($event->subject()->request->plugin) {
            $tableName = $event->subject()->request->plugin . '.' . $tableName;
        }
        // skip non-searchable models
        if (!$this->_isSearchable($tableName)) {
            return;
        }

        $url = [
            'plugin' => $event->subject()->request->plugin,
            'controller' => $event->subject()->request->controller,
            'action' => 'search'
        ];

        $aclTable = TableRegistry::get('RolesCapabilities.Capabilities');
        try {
            $aclTable->checkAccess($url, $user);
        } catch (ForbiddenException $e) {
            return;
        }

        $event->result = $event->subject()->element(static::SEARCH_FORM_ELEMENT);
    }
}
