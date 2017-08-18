<?php
namespace App\Event\View;

use App\Event\EventName;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Network\Exception\ForbiddenException;
use Cake\ORM\TableRegistry;
use Cake\View\View;
use RolesCapabilities\CapabilityTrait;
use Search\Controller\Traits\SearchableTrait;

class LayoutMenuListener implements EventListenerInterface
{
    use CapabilityTrait;
    use SearchableTrait;

    /**
     * Default search form element.
     */
    const SEARCH_FORM_ELEMENT = 'search-form';

    /**
     * Implemented Events
     *
     * @return array
     */
    public function implementedEvents()
    {
        return [
            (string)EventName::LAYOUT_ASIDE_FORM() => 'getSearchForm'
        ];
    }

    /**
     * Add search form in the layout.
     *
     * @param Cake\Event\Event $event Event object
     * @param array $user User info
     * @return void
     */
    public function getSearchForm(Event $event, array $user)
    {
        $cakeView = $event->subject();

        if ($this->_getSearchForm($event, $cakeView, $user)) {
            return;
        }
    }

    /**
     * Add search form if current model is searchable and user has search access to it.
     *
     * @param Cake\Event\Event $event Event object
     * @param Cake\View\View $cakeView View instance
     * @param array $user User info
     * @return bool
     */
    protected function _getSearchForm(Event $event, View $cakeView, array $user)
    {
        $tableName = $cakeView->name;
        if ($cakeView->plugin) {
            $tableName = $cakeView->plugin . '.' . $tableName;
        }
        $table = TableRegistry::get($tableName);

        // skip non-searchable models
        if (!$this->_isSearchable($table)) {
            return false;
        }

        $url = [
            'plugin' => $cakeView->plugin,
            'controller' => $cakeView->name,
            'action' => 'search'
        ];

        try {
            if (!$this->_checkAccess($url, $user)) {
                return false;
            }
        } catch (ForbiddenException $e) {
            return false;
        }

        $name = $cakeView->name;
        if (method_exists($table, 'moduleAlias')) {
            $name = $table->moduleAlias();
        }

        $event->result = $cakeView->element(static::SEARCH_FORM_ELEMENT, ['name' => $name]);

        return true;
    }
}
