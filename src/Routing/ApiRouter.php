<?php
namespace App\Routing;

use Cake\Core\App;
use Cake\Filesystem\Folder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

/**
 * ApiRouter Class
 *
 * Class responsible for setting up fallback
 * API routes, including API-versioned routes.
 *
 * Both `api/:controller/:action` and `api/v1.0/:controller/:action` would
 * point to the same `V1/V0` sub-namespace.
 *
 * API versions codebase is located within subdirs in attempt to isolate the
 * business logic of the application.
 *
 */
class ApiRouter
{
    /** @var array $_versions */
    protected $_versions = [];

    /**
     * Default constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->getVersions();
    }

    /**
     * Get API Versions based on subdirs
     *
     * Setting API version routes in $_versions.
     *
     * @return void
     */
    public function getVersions()
    {
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

    /**
     * Get API Route path
     *
     * @param string $version of the path
     * @return string with prefixes api path version.
     */
    protected function _getApiRoutePath($version)
    {
        return '/api/v' . $version;
    }

    /**
     * Get API Route prefix
     *
     * @param array $versions that contain subdirs of prefix
     * @return string with combined API routing.
     */
    protected function _getApiRoutePrefix($versions)
    {
        return 'api/v' . implode('/v', $versions);
    }

    /**
     * Setting Router API prefixes and nested API versiones
     *
     * @return void
     */
    public function setRoutes()
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
