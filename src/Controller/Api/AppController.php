<?php
namespace App\Controller\Api;

use App\Swagger\Annotation;
use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use CsvMigrations\Controller\Api\AppController as BaseController;
use RolesCapabilities\CapabilityTrait;

/**
    @SWG\Swagger(
        @SWG\Info(
            title="API Documentation",
            description="Interactive API documentation powered by Swagger.io",
            termsOfService="http://swagger.io/terms/",
            version="1.0.0"
        ),
        @SWG\SecurityScheme(
            securityDefinition="Bearer",
            description="Json Web Tokens (JWT)",
            type="apiKey",
            name="token",
            in="query"
        )
    )
 */
class AppController extends BaseController
{
    use CapabilityTrait;

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        if (Configure::read('API.auth')) {
            $this->enableAuthorization();
        }
    }

    /**
     * Enable API authorization checks.
     *
     * @return void
     */
    protected function enableAuthorization()
    {
        $hasAccess = $this->_checkAccess($this->request->params, $this->Auth->user());

        if (!$hasAccess) {
            throw new ForbiddenException();
        }

        $this->loadComponent('RolesCapabilities.Capability', [
            'currentRequest' => $this->request->params
        ]);
    }

    /**
     * Generates Swagger annotations
     *
     * Instantiates CsvAnnotation with required parameters
     * and returns its generated swagger annotation content.
     *
     * @param string $path File path
     * @return string
     */
    public static function generateSwaggerAnnotations($path)
    {
        $csvAnnotation = new Annotation(get_called_class(), $path);

        return $csvAnnotation->getContent();
    }
}
