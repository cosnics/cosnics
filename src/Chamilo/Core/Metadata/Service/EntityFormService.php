<?php
namespace Chamilo\Core\Metadata\Service;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Core\Metadata\Element\Service\ElementService;
use Chamilo\Core\Metadata\Schema\Instance\Storage\DataClass\SchemaInstance;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Utilities\UUID;
use Chamilo\Core\Metadata\Vocabulary\Storage\DataClass\Vocabulary;

/**
 *
 * @package Chamilo\Core\Metadata\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityFormService
{

    /**
     *
     * @var \Chamilo\Core\Metadata\Schema\Instance\Storage\DataClass\SchemaInstance
     */
    private $schemaInstance;

    /**
     *
     * @var \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    private $entity;

    /**
     *
     * @var \Chamilo\Libraries\Format\Form\FormValidator
     */
    private $formValidator;

    /**
     *
     * @param \Chamilo\Core\Metadata\Schema\Instance\Storage\DataClass\SchemaInstance $schemaInstance
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     * @param \Chamilo\Libraries\Format\Form\FormValidator $formValidator
     */
    public function __construct(SchemaInstance $schemaInstance, DataClass $entity, FormValidator $formValidator)
    {
        $this->schemaInstance = $schemaInstance;
        $this->entity = $entity;
        $this->formValidator = $formValidator;
    }

    /**
     *
     * @return \Chamilo\Core\Metadata\Schema\Instance\Storage\DataClass\SchemaInstance
     */
    public function getSchemaInstance()
    {
        return $this->schemaInstance;
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Schema\Instance\Storage\DataClass\SchemaInstancea $schemaInstance
     */
    public function setSchemaInstance($schemaInstance)
    {
        $this->schemaInstance = $schemaInstance;
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Form\FormValidator
     */
    public function getFormValidator()
    {
        return $this->formValidator;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Form\FormValidator $formValidator
     */
    public function setFormValidator($formValidator)
    {
        $this->formValidator = $formValidator;
    }

    public function addElements()
    {
        $this->addDependencies();

        $elementService = new ElementService();
        $elements = $elementService->getElementsForSchemaInstance($this->schemaInstance);

        while ($element = $elements->next_result())
        {
            $elementName = EntityService :: PROPERTY_METADATA_SCHEMA . '[' . $this->schemaInstance->get_schema_id() .
                 '][' . $this->schemaInstance->get_id() . '][' . $element->get_id() . ']';

            if ($element->usesVocabulary())
            {
                $uniqueIdentifier = UUID :: v4();

                $class = 'metadata-input';
                if ($element->isVocabularyUserDefined())
                {
                    $class .= ' metadata-input-new';
                }

                $tagElementGroup = array();
                $tagElementGroup[] = $this->formValidator->createElement(
                    'text',
                    $elementName . '[' . EntityService :: PROPERTY_METADATA_SCHEMA_EXISTING . ']',
                    null,
                    array(
                        'id' => $uniqueIdentifier,
                        'class' => $class,
                        'data-schema-id' => $this->schemaInstance->get_schema_id(),
                        'data-schema-instance-id' => $this->schemaInstance->get_id(),
                        'data-element-id' => $element->get_id(),
                        'data-element-value-limit' => $element->get_value_limit()));

                if ($element->isVocabularyUserDefined())
                {
                    $tagElementGroup[] = $this->formValidator->createElement(
                        'hidden',
                        $elementName . '[' . EntityService :: PROPERTY_METADATA_SCHEMA_NEW . ']',
                        null,
                        array('id' => 'new-' . $uniqueIdentifier));
                }

                $urlRenderer = new Redirect(
                    array(
                        Application :: PARAM_CONTEXT => \Chamilo\Core\Metadata\Vocabulary\Ajax\Manager :: context(),
                        Application :: PARAM_ACTION => \Chamilo\Core\Metadata\Vocabulary\Ajax\Manager :: ACTION_SELECT,
                        \Chamilo\Core\Metadata\Vocabulary\Ajax\Manager :: PARAM_ELEMENT_IDENTIFIER => $uniqueIdentifier,
                        \Chamilo\Core\Metadata\Element\Manager :: PARAM_ELEMENT_ID => $element->get_id()));
                $vocabularyUrl = $urlRenderer->getUrl();
                $onclick = 'vocabulary-selector" onclick="javascript:openPopup(\'' . $vocabularyUrl .
                     '\'); return false;';

                $vocabularyAction = new ToolbarItem(
                    Translation :: get('ShowVocabulary'),
                    Theme :: getInstance()->getImagePath(
                        'Chamilo\Core\Metadata\Element',
                        'ValueType/' . $element->get_value_type()),
                    $vocabularyUrl,
                    ToolbarItem :: DISPLAY_ICON,
                    false,
                    $onclick,
                    '_blank');

                $tagElementGroup[] = $this->formValidator->createElement(
                    'static',
                    null,
                    null,
                    $vocabularyAction->as_html());

                $this->formValidator->addGroup($tagElementGroup, null, $element->get_display_name(), null, false);
            }
            else
            {
                $this->formValidator->addElement(
                    'textarea',
                    $elementName,
                    $element->get_display_name(),
                    array('cols' => 60, 'rows' => 6, 'maxlength' => 1000));
            }
        }
    }

    /**
     * Adds the dependencies to the form
     */
    private function addDependencies()
    {
        $resource_manager = ResourceManager :: get_instance();
        $plugin_path = Path :: getInstance()->getJavascriptPath('Chamilo\Core\Metadata', true) .
             'Plugin/Bootstrap/Tagsinput/';

        $dependencies = array();

        $dependencies[] = $resource_manager->get_resource_html($plugin_path . 'bootstrap-typeahead.js');
        $dependencies[] = $resource_manager->get_resource_html($plugin_path . 'bootstrap-tagsinput.js');
        $dependencies[] = $resource_manager->get_resource_html($plugin_path . 'bootstrap-tagsinput.css');
        $dependencies[] = $resource_manager->get_resource_html(
            Path :: getInstance()->getJavascriptPath('Chamilo\Core\Metadata', true) . 'Input.js');

        $this->formValidator->addElement('html', implode(PHP_EOL, $dependencies));
    }

    public function setDefaults()
    {
        $defaults = array();

        $elementService = new ElementService();
        $elements = $elementService->getElementsForSchemaInstance($this->schemaInstance);

        while ($element = $elements->next_result())
        {
            if ($element->usesVocabulary())
            {
                $elementName = EntityService :: PROPERTY_METADATA_SCHEMA . '[' . $this->schemaInstance->get_schema_id() .
                     '][' . $this->schemaInstance->get_id() . '][' . $element->get_id() . '][' .
                     EntityService :: PROPERTY_METADATA_SCHEMA_EXISTING . ']';

                $options = array();

                $elementInstanceVocabularies = $elementService->getElementInstanceVocabulariesForSchemaInstanceAndElement(
                    $this->schemaInstance,
                    $element)->as_array();

                if (count($elementInstanceVocabularies) > 0)
                {
                    foreach ($elementInstanceVocabularies as $elementInstanceVocabulary)
                    {
                        $item = new \stdClass();
                        $item->id = $elementInstanceVocabulary->get_id();
                        $item->value = $elementInstanceVocabulary->get_value();

                        $options[] = $item;
                    }
                }

                $elementValue = json_encode($options);
            }
            else
            {
                $elementName = EntityService :: PROPERTY_METADATA_SCHEMA . '[' . $this->schemaInstance->get_schema_id() .
                     '][' . $this->schemaInstance->get_id() . '][' . $element->get_id() . ']';
                $elementInstanceVocabulary = $elementService->getElementInstanceVocabularyForSchemaInstanceAndElement(
                    $this->schemaInstance,
                    $element);

                if ($elementInstanceVocabulary instanceof Vocabulary)
                {
                    $elementValue = $elementInstanceVocabulary->get_value();
                }
                else
                {
                    $elementValue = '';
                }
            }

            $defaults[$elementName] = $elementValue;
        }

        $this->formValidator->setDefaults($defaults);
    }
}
