<?php
namespace App\Event\Plugin\CsvMigrations\View;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Log\LogTrait;
use Cake\Network\Exception\ForbiddenException;
use Cake\Utility\Inflector;
use CsvMigrations\FieldHandlers\FieldHandlerFactory;
use CsvMigrations\Event\EventName;
use Qobo\Utils\ModuleConfig\ModuleConfig;
use RolesCapabilities\CapabilityTrait;

class TranslationViewListener implements EventListenerInterface
{
    use CapabilityTrait;
    use LogTrait;

    /**
     * @return array of implemented events for sets module
     */
    public function implementedEvents()
    {
        return [
            (string)EventName::VIEW_TRANSLATION_BUTTON() => 'getTranslationButton',
        ];
    }

    /**
     *  processFieldValue method
     *
     *  Returns back processed field's value as per its DB type
     *
     * @param Cake\Event\Event $event of the current request
     * @param string $model model name
     * @param array $options field name, field value, record ID etc
     * @return string processed field value
     */
    public function getTranslationButton(Event $event, $model, $options = [])
    {
        $result = '';

        $isTranslatable = $this->_isTranslatable($model, $options['field_name']);

        $hasRights = $this->_hasRightsToTranslate($options['user']);

        if ($isTranslatable && $hasRights && !empty($options['field_value'])) {
            $result = '<a href="#translations_translate_id_modal" data-toggle="modal" data-record="' . $options['record_id'] .
                '" data-model="' . $model . '" data-field="' . $options['field_name'] . '" data-value="' . $options['field_value'] . '"><i class="fa fa-globe"></i></a>&nbsp;';
        }

        return $result;
    }

    /**
     *  _isTranslatable method
     *
     * @param string $model target model
     * @param string $field target field name
     * @return bool true in case of model and field are translatable
     */
    protected function _isTranslatable($model, $field)
    {
        $translatableModule = false;
        $translatableField = false;
        $result = false;

        // Read translatable from config.ini
        $mc = new ModuleConfig(ModuleConfig::CONFIG_TYPE_MODULE, Inflector::camelize($model));
        $moduleConfig = (array)json_decode(json_encode($mc->parse()), true);
        $translatableModule = empty($moduleConfig['table']['translatable']) ? false : (bool)$moduleConfig['table']['translatable'];

        // Read field options from fields.ini
        $mc = new ModuleConfig(ModuleConfig::CONFIG_TYPE_FIELDS, Inflector::camelize($model));
        $fieldOptions = (array)json_decode(json_encode($mc->parse()), true);
        $translatableField = empty($fieldOptions[$field]['translatable']) ? false : (bool)$fieldOptions[$field]['translatable'];

        // Set showTranslateButton based on both module and field configuration
        $result = $translatableModule ? $translatableField : false;

        return $result;
    }

    /**
     *  _hasRightsToTranslate method
     *
     * @param object $user current user
     * @return bool true in case current user has rights to translate
     */
    protected function _hasRightsToTranslate($user)
    {
        $result = false;

        $url = [
            'plugin' => 'Translations',
            'controller' => 'Translations',
            'action' => 'addOrUpdate'
        ];

        if ($this->_checkAccess($url, $user)) {
            $result = true;
        }

        return $result;
    }
}
