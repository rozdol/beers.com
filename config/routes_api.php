<?php
use Cake\Core\App;
use Cake\Filesystem\Folder;
use Cake\Routing\Router;

class ApiRoutes
{
    protected $_versions = [];

    public function __construct() {
        $this->_getVersions();
        $this->_setRoutes();
    }

    protected function _getVersions() {
        $apiPath = App::path('Controller/Api')[0];

        $dir = new Folder();
        // get folders in Controller/Api directory
        $tree = $dir->tree($apiPath, false, 'dir');

        foreach ($tree as $treePath) {
            if ($treePath === $apiPath) {
                continue;
            }

            $path = str_replace($apiPath, '', $treePath);

            preg_match('/V(\d+)\/V(\d+)/', $path, $matches);
            if (empty($matches)) {
                continue;
            }

            unset($matches[0]);
            $number = implode('.', $matches);

            $this->_versions[] = [
                'number' => $number,
                'prefix' => $this->_getApiRoutePrefix($matches),
                'path' => $this->_getApiRoutePath($number),
            ];
        }
    }

    protected function _getApiRoutePath($version)
    {
        return '/api/v' . $version;
    }

    protected function _getApiRoutePrefix($versions)
    {
        return 'api/v' . implode('/v', $versions);
    }

    protected function _setRoutes()
    {
        $versions = $this->_versions;
        $default = 'api/v1/v0';

        Router::scope('/api', function ($routes) use ($versions, $default) {
            // Setting up fallback non-versioned API url calls.
            // It can handle `api/controller/index.json` as well
            // as `api/controller.json` calls.
            Router::prefix('api', function ($routes) use ($default) {

                $routes->extensions(['json']);
                $routes->connect('/:controller', ['prefix' => $default], ['routeClass' => DashedRoute::class]);
                $routes->connect('/:controller/:action/*', ['prefix' => $default], ['routeClass' => DashedRoute::class]);
                $routes->fallbacks(DashedRoute::class);
            });

            foreach ($versions as $version) {
                Router::prefix($version['prefix'], ['path' => $version['path']], function ($routes) {
                    $routes->extensions(['json']);
                    $routes->fallbacks(DashedRoute::class);
                });
            }
        });
    }
}

new ApiRoutes();
