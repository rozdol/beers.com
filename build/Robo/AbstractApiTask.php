<?php

namespace Qobo\Robo;

use \Robo\Result;
use \Robo\Exception\TaskException;

/**
 * Qobo base task.
 */
abstract class AbstractApiTask extends AbstractTask
{
    /**
     * @var string $apiConfig path to API config
     */
    protected $apiConfigPath = "apiConfig";

    /**
     * @var string $apiConfigInstanceKey key under apiConfigPath for API instance Object
     */
    protected $apiConfigInstanceKey = "api_instance";

    /**
     * @var array $apiConfig API class name and params
     */
    protected $apiConfig = [
        'class'     => null,
        'params'    => null
    ];

    /**
     * @var Object $api
     */
    protected $api = null;

    /**
     * {inheritdoc}
     */
    public function __construct($apiConfig = null)
    {
        if ($apiConfig != null) {
            $this->apiConfig = array_merge($this->apiConfig, $apiConfig);
        }
    }

    /**
     * {inheritdoc}
     */
    public function run()
    {
        $result = parent::run();
        if (!$result->wasSuccessful()) {
            return $result;
        }
        $this->apiInit();

        return Result::success($this, "");
    }

    /**
     * Try to init the Api
     */
    protected function apiInit()
    {
        // get apiConfig and merge it with whatever we already have
        $apiConfig = $this->getConfigValue($this->apiConfigPath);
        if (is_array($apiConfig)) {
            $this->apiConfig = array_merge($apiConfig, array_filter($this->apiConfig));
        }

        $apiClass = $this->apiConfig['class'];

        // check if someone already initiated API for us and stored its
        // instance in the config
        if ($this->api == null) {
            $this->api = $this->getConfigValue($this->apiConfigPath . "." . $this->apiConfigInstanceKey);
        }

        // if we have a proper API, no need to do any other work
        if ($this->api != null && $this->api instanceof $apiClass) {
            return;
        }

        $this->printInfo("Initializing API");

        // verify the API class is correct and we can use it
        if (!class_exists($apiClass)) {
            throw new TaskException(get_called_class(), "Cannot find '$apiClass' to use as API class");
        }

        // get any params
        $apiParams = null;
        if (isset($this->apiConfig['params'])) {
            $apiParams = $this->apiConfig['params'];
        }

        // init API
        try {
            $this->api = new $apiClass($apiParams);
        } catch (\Exception $e) {
            throw new TaskException(get_called_class(), "Failed to init '$apiClass' API: " . $e->getMessage());
        }

        // store API instance in the config for any possible later use
        static::configure($this->apiConfigPath . "." . $this->apiConfigInstanceKey, $this->api);
    }
}
