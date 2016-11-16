<?php
namespace App\Auth;

use Cake\Auth\BaseAuthenticate;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Log\LogTrait;
use Cake\Network\Exception\InternalErrorException;
use Cake\Network\Exception\UnauthorizedException;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\ORM\TableRegistry;
use ErrorException;
use Psr\Log\LogLevel;

class LdapAuthenticate extends BaseAuthenticate
{
    use LogTrait;

    /**
     * Default LDAP protocol version.
     */
    const DEFAULT_VERSION = 3;

    /**
     * Default LDAP port.
     */
    const DEFAULT_PORT = 389;

    /**
     * LDAP Object.
     *
     * @var object
     */
    protected $_connection;

    /**
     * LDAP account.
     *
     * @var string
     */
    protected $_account;

    /**
     * {@inheritDoc}
     */
    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);

        // set LDAP configuration
        $this->config(Configure::read('Ldap'));

        if (empty($this->_config['host'])) {
            throw new InternalErrorException('LDAP Server not specified.');
        }

        if (empty($this->_config['version'])) {
            $this->_config['version'] = static::DEFAULT_VERSION;
        }

        if (empty($this->_config['port'])) {
            $this->_config['port'] = static::DEFAULT_PORT;
        }

        $this->_connect();
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(Request $request, Response $response)
    {
        $this->_setAccount($request);
        $user = $this->getUser($request);
        if ($user) {
            $user = $this->_saveUser($user);
        }

        return $user;
    }

    /**
     * LDAP connect
     *
     * @return void
     */
    protected function _connect()
    {
        try {
            $this->_connection = @ldap_connect($this->_config['host'], $this->_config['port']);
            // set LDAP options
            ldap_set_option($this->_connection, LDAP_OPT_PROTOCOL_VERSION, (int)$this->_config['version']);
            ldap_set_option($this->_connection, LDAP_OPT_REFERRALS, 0);
            ldap_set_option($this->_connection, LDAP_OPT_NETWORK_TIMEOUT, 5);
        } catch (Exception $e) {
            $this->log('Unable to connect to specified LDAP Server.', LogLevel::CRITICAL);
        }
    }

    /**
     * Set LDAP account.
     *
     * @param Cake\Network\Request $request Request object
     * @return void
     */
    protected function _setAccount(Request $request)
    {
        if (!isset($request->data['username'])) {
            return;
        }

        $this->_account = $request->data['username'];

        $baseDn = explode(',', $this->_config['baseDn']);

        if (empty($baseDn)) {
            return;
        }

        $dc = [];
        foreach ($baseDn as $value) {
            if (0 === stripos($value, 'dc=')) {
                $dc[] = str_ireplace('dc=', '', $value);
            }
        }

        if (empty($dc)) {
            return;
        }

        $this->_account = $request->data['username'] . '@' . implode('.', $dc);
    }

    /**
     * {@inheritDoc}
     */
    public function getUser(Request $request)
    {
        if (empty($this->_account) || !isset($request->data['password'])) {
            return false;
        }

        try {
            $bind = @ldap_bind($this->_connection, $this->_account, $request->data['password']);
            if ($bind) {
                // on bind success return username
                return ['username' => $request->data['username']];
            } else {
                $this->log('LDAP server bind failed.', LogLevel::CRITICAL);
            }
        } catch (Exception $e) {
            $this->log($e->getMessage());
        }

        return false;
    }

    /**
     * Save LDAP user to the Database.
     *
     * @param  array      $data User info
     * @return array|bool       User info or false if failed.
     */
    protected function _saveUser(array $data = [])
    {
        // return false if user data empty or username field is not set
        if (empty($data) || empty($data[$this->_config['fields']['username']])) {
            return false;
        }

        $table = TableRegistry::get($this->_config['userModel']);

        // look for the user in the database
        $query = $table->find('all', [
            'conditions' => [$this->_config['fields']['username'] => $data[$this->_config['fields']['username']]]
        ]);
        $entity = $query->first();

        // user already exists, just return the existing entity
        if ($entity) {
            return $entity->toArray();
        }

        // use random password for local entity of ldap user
        $data[$this->_config['fields']['password']] = uniqid();

        // activate user by default
        $data['active'] = true;

        // set email the same as username
        $data['email'] = $this->_account;

        // save new user entity
        $entity = $table->newEntity();
        $entity = $table->patchEntity($entity, $data);

        if ($table->save($entity)) {
            return $entity->toArray();
        } else {
            return false;
        }
    }

    /**
     * Destructor method.
     *
     * @return void
     */
    public function __destruct()
    {
        $this->_disconnect();
    }

    /**
     * Disconnect LDAP connection.
     *
     * @return void
     */
    protected function _disconnect()
    {
        @ldap_unbind($this->_connection);
    }
}
