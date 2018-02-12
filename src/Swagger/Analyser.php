<?php
namespace App\Swagger;

use Cake\Core\App;
use Swagger\Context;
use Swagger\StaticAnalyser;

class Analyser extends StaticAnalyser
{
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
        if ($className) {
            $annotations = $className::generateSwaggerAnnotations($filename);
            $tokens = token_get_all($annotations);
        }

        return $this->fromTokens($tokens, new Context(['filename' => $filename]));
    }
}
