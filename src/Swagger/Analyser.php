<?php
namespace App\Swagger;

use App\Feature\Factory as FeatureFactory;
use Cake\Core\App;
use Qobo\Utils\ModuleConfig\ConfigType;
use Qobo\Utils\ModuleConfig\ModuleConfig;
use Swagger\Context;
use Swagger\StaticAnalyser;

class Analyser extends StaticAnalyser
{
    /**
     * Flag for including Swagger Info annotation.
     *
     * @var bool
     */
    private $withInfo = true;

    /**
     * Extract and process all doc-comments from an
     * auto-generated swagger annotations content.
     *
     * @param string $filename Path to a php file.
     * @return \Swagger\Analysis
     */
    public function fromFile($filename)
    {
        $className = basename($filename, '.php');
        $className = App::className($className, 'Controller/Api/V1/V0');

        $tokens = [];
        $module = basename($filename, 'Controller.php');
        if (static::allowsSwagger($module) && $className) {
            $annotations = $className::generateSwaggerAnnotations($module, $filename, $this->withInfo);
            $tokens = token_get_all($annotations);
            $this->withInfo = false;
        }

        return $this->fromTokens($tokens, new Context(['filename' => $filename]));
    }

    /**
     * Validates if module allows Swagger annotations generation.
     *
     * @param string $module Module name
     * @return bool
     */
    private static function allowsSwagger($module)
    {
        if (in_array($module, ['Users'])) {
            return true;
        }

        $config = (new ModuleConfig(ConfigType::MIGRATION(), $module))->parse();
        $config = json_decode(json_encode($config), true);
        if (empty($config)) {
            return false;
        }

        $feature = FeatureFactory::get('Module' . DS . $module);
        if ($feature->isSwaggerActive()) {
            return true;
        }

        return false;
    }
}
