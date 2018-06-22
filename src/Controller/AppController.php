<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use App\Controller\ChangelogTrait;
use App\Event\Plugin\Search\Model\SearchableFieldsListener;
use App\Feature\Factory as FeatureFactory;
use AuditStash\Meta\RequestMetadata;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\Utility\Security;
use Exception;
use Firebase\JWT\JWT;
use Qobo\Utils\ModuleConfig\ConfigType;
use Qobo\Utils\ModuleConfig\ModuleConfig;
use RolesCapabilities\Access\AccessFactory;
use RolesCapabilities\Capability;
use RolesCapabilities\CapabilityTrait;
use Search\Controller\SearchTrait;
use Search\Utility as SearchUtility;
use Search\Utility\Search;
use Search\Utility\Validator as SearchValidator;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    use CapabilityTrait;
    use ChangelogTrait;
    use SearchTrait;

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('Csrf');
        $this->loadComponent('CakeDC/Users.UsersAuth');

        $this->Auth->config('authorize', false);
        $this->Auth->config('loginRedirect', '/');
        $this->Auth->config('flash', ['element' => 'error', 'key' => 'auth']);

        // enable LDAP authentication
        if ((bool)Configure::read('Ldap.enabled')) {
            $this->Auth->config('authenticate', ['Ldap']);
        }

        // prevent access on disabled module
        $feature = FeatureFactory::get('Module' . DS . $this->name);
        if (!$feature->isActive()) {
            throw new NotFoundException();
        }

        $this->loadComponent('RolesCapabilities.Capability', [
            'currentRequest' => $this->request->params
        ]);
    }

    /**
     * beforeRender callback
     *
     * @param \Cake\Event\Event $event Event
     * @return void
     */
    public function beforeRender(Event $event)
    {
        $this->set('user', $this->Auth->user());
    }

    /**
     * Callack method.
     *
     * @param  \Cake\Event\Event $event Event object
     * @return void|\Cake\Http\Response
     */
    public function beforeFilter(Event $event)
    {
        $this->_allowedResetPassword();

        // if user not logged in, redirect him to login page
        $url = $event->subject()->request->params;
        try {
            $result = $this->_checkAccess($url, $this->Auth->user());
            if (!$result) {
                throw new ForbiddenException();
            }
        } catch (ForbiddenException $e) {
            if (empty($this->Auth->user())) {
                $this->redirect('/login');
            } else {
                // send empty response for embedded forms
                if ($this->request->query('embedded')) {
                    return $this->response;
                }
                throw new ForbiddenException($e->getMessage());
            }
        }

        if (method_exists($this, '_getSkipActions')) {
            $this->Auth->allow($this->_getSkipActions($url));
        }

        $this->_setIframeRendering();

        EventManager::instance()->on(new RequestMetadata($this->request, $this->Auth->user('id')));

        $this->_generateApiToken();

        // Load AdminLTE theme
        $this->loadAdminLTE();
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $table = TableRegistry::getTableLocator()->get('Search.SavedSearches');

        $entity = $table->find()
            ->where(['SavedSearches.model' => $this->name, 'SavedSearches.system' => true])
            ->firstOrFail();

        $searchData = json_decode($entity->content, true);

        // return json response and skip any further processing.
        if ($this->request->is('ajax') && $this->request->accepts('application/json')) {
            $this->viewBuilder()->className('Json');
            $response = $this->getAjaxViewVars(
                $searchData['latest'],
                $this->{$this->name},
                new Search($this->{$this->name}, $this->Auth->user())
            );
            $this->set($response);

            return;
        }

        $this->set([
            'entity' => $entity,
            'searchData' => $searchData['latest'],
            'preSaveId' => (new Search($this->{$this->name}, $this->Auth->user()))->create($searchData['latest']),
            'searchableFields' => SearchableFieldsListener::getSearchableFieldsByTable(
                $this->{$this->name},
                $this->Auth->user()
            ),
            'associationLabels' => SearchUtility::instance()->getAssociationLabels($this->{$this->name})
        ]);

        $this->render('/Module/index');
    }

    /**
     * Setup AdminLTE theme
     *
     * This is just to keep the `beforeFilter()` smaller and
     * simpler, as well as to provide extending classes a way
     * to adjust things, if necessary.
     *
     * @return void
     */
    protected function loadAdminLTE()
    {
        $loadAdminLTE = true;

        // Skip AdminLTE on JSON requests
        if ($this->request->is('json')) {
            $loadAdminLTE = false;
        }

        // Skip AdminLTE on AJAX requests
        if ($this->request->is('ajax')) {
            $loadAdminLTE = false;
        }

        // Load AdminLTE for regular requests
        if ($loadAdminLTE) {
            $this->viewBuilder()->className('AdminLTE.AdminLTE');
        }

        $this->viewBuilder()->theme('AdminLTE');
        $this->viewBuilder()->layout('adminlte');

        $title = Inflector::humanize(Inflector::underscore($this->name));
        try {
            $mc = new ModuleConfig(ConfigType::MODULE(), $this->name);
            $config = $mc->parse();
            if (!empty($config->table->alias)) {
                $title = $config->table->alias;
            }
        } catch (Exception $e) {
            // do nothing
        }

        // overwrite theme title before setting the theme
        // NOTE: we set controller specific title, to work around requestAction() calls.
        Configure::write('Theme.title.' . $this->name, $title);
        $this->set('theme', Configure::read('Theme'));
    }

    /**
     * Check if allowed requestResetPassword action is allowed.
     *
     * @return void
     */
    protected function _allowedResetPassword()
    {
        $url = [
            'plugin' => 'CakeDC/Users',
            'controller' => 'Users',
            'action' => 'requestResetPassword'
        ];

        // skip if url does not match Users requestResetPassword action.
        if (array_diff_assoc($url, $this->request->params)) {
            return;
        }

        // allow if LDAP is not enabled.
        if (!(bool)Configure::read('Ldap.enabled')) {
            return;
        }

        throw new NotFoundException();
    }

    /**
     * Method that generates API token for internal use.
     *
     * @return void
     */
    protected function _generateApiToken()
    {
        Configure::write('API.token', JWT::encode(
            [
                'sub' => $this->Auth->user('id'),
                'exp' => time() + 604800
            ],
            Security::salt()
        ));

        Configure::write('CsvMigrations.api.token', Configure::read('API.token'));
        Configure::write(
            'CsvMigrations.BootstrapFileInput.defaults.ajaxSettings.headers.Authorization',
            'Bearer ' . Configure::read('API.token')
        );
        Configure::write('Search.api.token', Configure::read('API.token'));
    }

    /**
     * Allow/Prevent page rendering in iframe.
     *
     * @return void
     */
    protected function _setIframeRendering()
    {
        $renderIframe = trim((string)getenv('ALLOW_IFRAME_RENDERING'));

        if ('' !== $renderIframe) {
            $this->response->header('X-Frame-Options', $renderIframe);
        }
    }

    /**
     * Get list of controller's skipped actions.
     *
     * @param  string $controllerName Controller name
     * @return array
     */
    public static function getSkipActions($controllerName)
    {
        $result = [
            'getMenu',
            'getCapabilities',
            'getSkipControllers',
            'getSkipActions'
        ];

        return $result;
    }
}
