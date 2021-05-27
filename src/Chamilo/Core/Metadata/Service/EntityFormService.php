<?php
namespace Chamilo\Core\Metadata\Service;

use Chamilo\Core\Metadata\Element\Manager;
use Chamilo\Core\Metadata\Element\Service\ElementService;
use Chamilo\Core\Metadata\Entity\DataClassEntity;
use Chamilo\Core\Metadata\Provider\Exceptions\NoProviderAvailableException;
use Chamilo\Core\Metadata\Provider\Service\PropertyProviderService;
use Chamilo\Core\Metadata\Storage\DataClass\SchemaInstance;
use Chamilo\Core\Metadata\Storage\DataClass\Vocabulary;
use Chamilo\Core\Metadata\Vocabulary\Service\VocabularyService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\UUID;
use stdClass;

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
     * @var \Chamilo\Core\Metadata\Provider\Service\PropertyProviderService
     */
    private $propertyProviderService;

    /**
     * @var \Chamilo\Core\Metadata\Vocabulary\Service\VocabularyService
     */
    private $vocabularyService;

    /**
     * @var \Chamilo\Core\Metadata\Element\Service\ElementService
     */
    private $elementService;

    /**
     * @param \Chamilo\Core\Metadata\Provider\Service\PropertyProviderService $propertyProviderService
     * @param \Chamilo\Core\Metadata\Vocabulary\Service\VocabularyService $vocabularyService
     * @param \Chamilo\Core\Metadata\Element\Service\ElementService $elementService
     */
    public function __construct(
        PropertyProviderService $propertyProviderService, VocabularyService $vocabularyService,
        ElementService $elementService
    )
    {
        $this->propertyProviderService = $propertyProviderService;
        $this->vocabularyService = $vocabularyService;
        $this->elementService = $elementService;
    }

    /**
     * @param \Chamilo\Libraries\Format\Form\FormValidator $formValidator
     */
    private function addDependencies(FormValidator $formValidator)
    {
        $resource_manager = ResourceManager::getInstance();
        $plugin_path = Path::getInstance()->getPluginPath('Chamilo\Core\Metadata', true) . 'Bootstrap/Tagsinput/';

        $dependencies = [];

        $dependencies[] = $resource_manager->getResourceHtml($plugin_path . 'bootstrap-typeahead.js');
        $dependencies[] = $resource_manager->getResourceHtml($plugin_path . 'bootstrap-tagsinput.js');
        $dependencies[] = $resource_manager->getResourceHtml($plugin_path . 'bootstrap-tagsinput.css');
        $dependencies[] = $resource_manager->getResourceHtml(
            Path::getInstance()->getJavascriptPath('Chamilo\Core\Metadata', true) . 'Input.js'
        );

        $formValidator->addElement('html', implode(PHP_EOL, $dependencies));
    }

    /**
     * @param \Chamilo\Libraries\Format\Form\FormValidator $formValidator
     * @param \Chamilo\Core\Metadata\Storage\DataClass\SchemaInstance $schemaInstance
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function addElements(
        FormValidator $formValidator, SchemaInstance $schemaInstance, DataClassEntity $entity, User $user
    )
    {
        $this->addDependencies($formValidator);

        $elements = $this->getElementService()->getElementsForSchemaInstance($schemaInstance);

        foreach($elements as $element)
        {
            try
            {
                $providedIcon = new FontAwesomeGlyph(
                    'lock', [], Translation::get('ProvidedMetadataElementValue', null, 'Chamilo\Core\Metadata'),
                    'fas'
                );

                $providedIcon = '<span class="locked-row-label">' . $providedIcon->render() . '</span>';
                $displayName = $element->get_display_name() . $providedIcon;

                if ($element->usesVocabulary())
                {
                    $providedVocabularies =
                        $this->getVocabularyService()->getProvidedVocabulariesForUserEntitySchemaInstanceElement(
                            $user, $entity, $schemaInstance, $element
                        );

                    $html = [];

                    $html[] = '<div class="locked-tags">';

                    foreach ($providedVocabularies as $providedVocabulary)
                    {
                        $html[] = '<span class="locked-tag locked-action">';
                        $html[] = $providedVocabulary->get_value();
                        $html[] = '</span>';
                    }

                    $html[] = '</div>';

                    $formValidator->addElement('static', null, $displayName, implode(PHP_EOL, $html));
                }
                else
                {
                    $providedValue = $this->getVocabularyService()->getProvidedValueForUserEntitySchemaInstanceElement(
                        $user, $entity, $schemaInstance, $element
                    );

                    $html = [];

                    $html[] = '<div class="provided-element-value locked-action">';
                    $html[] = $providedValue;
                    $html[] = '</div>';

                    $formValidator->addElement('static', null, $displayName, implode(PHP_EOL, $html));
                }
            }
            catch (NoProviderAvailableException $exception)
            {
                $elementName = EntityService::PROPERTY_METADATA_SCHEMA . '[' . $schemaInstance->get_schema_id() . '][' .
                    $schemaInstance->getId() . '][' . $element->get_id() . ']';

                if ($element->usesVocabulary())
                {

                    $uniqueIdentifier = UUID::v4();

                    $class = 'metadata-input';
                    if ($element->isVocabularyUserDefined())
                    {
                        $class .= ' metadata-input-new';
                    }

                    $tagElementGroup = [];
                    $tagElementGroup[] = $formValidator->createElement(
                        'text', $elementName . '[' . EntityService::PROPERTY_METADATA_SCHEMA_EXISTING . ']', null,
                        array(
                            'id' => $uniqueIdentifier,
                            'class' => $class,
                            'data-schema-id' => $schemaInstance->get_schema_id(),
                            'data-schema-instance-id' => $schemaInstance->getId(),
                            'data-element-id' => $element->get_id(),
                            'data-element-value-limit' => $element->get_value_limit()
                        )
                    );

                    if ($element->isVocabularyUserDefined())
                    {
                        $tagElementGroup[] = $formValidator->createElement(
                            'hidden', $elementName . '[' . EntityService::PROPERTY_METADATA_SCHEMA_NEW . ']', null,
                            array('id' => 'new-' . $uniqueIdentifier)
                        );
                    }

                    $urlRenderer = new Redirect(
                        array(
                            Application::PARAM_CONTEXT => \Chamilo\Core\Metadata\Vocabulary\Ajax\Manager::context(),
                            Application::PARAM_ACTION => \Chamilo\Core\Metadata\Vocabulary\Ajax\Manager::ACTION_SELECT,
                            \Chamilo\Core\Metadata\Vocabulary\Ajax\Manager::PARAM_ELEMENT_IDENTIFIER => $uniqueIdentifier,
                            Manager::PARAM_ELEMENT_ID => $element->get_id()
                        )
                    );
                    $vocabularyUrl = $urlRenderer->getUrl();
                    $onclick =
                        'vocabulary-selector" onclick="javascript:openPopup(\'' . $vocabularyUrl . '\'); return false;';

                    $vocabularyAction = new ToolbarItem(
                        Translation::get('ShowVocabulary'), $element->getGlyph(), $vocabularyUrl,
                        ToolbarItem::DISPLAY_ICON, false, $onclick, '_blank'
                    );

                    $tagElementGroup[] = $formValidator->createElement(
                        'static', null, null, $vocabularyAction->render()
                    );

                    $formValidator->addGroup($tagElementGroup, null, $element->get_display_name(), null, false);
                }
                else
                {
                    $formValidator->addElement(
                        'textarea', $elementName, $element->get_display_name(),
                        array('cols' => 60, 'rows' => 6, 'maxlength' => 1000)
                    );
                }
            }
        }
    }

    /**
     * @return \Chamilo\Core\Metadata\Element\Service\ElementService
     */
    public function getElementService(): ElementService
    {
        return $this->elementService;
    }

    /**
     * @param \Chamilo\Core\Metadata\Element\Service\ElementService $elementService
     */
    public function setElementService(ElementService $elementService): void
    {
        $this->elementService = $elementService;
    }

    /**
     * @return \Chamilo\Core\Metadata\Provider\Service\PropertyProviderService
     */
    public function getPropertyProviderService(): PropertyProviderService
    {
        return $this->propertyProviderService;
    }

    /**
     * @param \Chamilo\Core\Metadata\Provider\Service\PropertyProviderService $propertyProviderService
     */
    public function setPropertyProviderService(
        PropertyProviderService $propertyProviderService
    ): void
    {
        $this->propertyProviderService = $propertyProviderService;
    }

    /**
     * @return \Chamilo\Core\Metadata\Vocabulary\Service\VocabularyService
     */
    public function getVocabularyService(): VocabularyService
    {
        return $this->vocabularyService;
    }

    /**
     * @param \Chamilo\Core\Metadata\Vocabulary\Service\VocabularyService $vocabularyService
     */
    public function setVocabularyService(VocabularyService $vocabularyService): void
    {
        $this->vocabularyService = $vocabularyService;
    }

    /**
     * @param \Chamilo\Core\Metadata\Storage\DataClass\SchemaInstance $schemaInstance
     * @param \Chamilo\Core\Metadata\Entity\DataClassEntity $entity
     * @param \Chamilo\Libraries\Format\Form\FormValidator $formValidator
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function setDefaults(
        SchemaInstance $schemaInstance, DataClassEntity $entity, FormValidator $formValidator, User $user
    )
    {
        $defaults = [];

        $elements = $this->getElementService()->getElementsForSchemaInstance($schemaInstance);

        foreach($elements as $element)
        {
            try
            {
                $providerLink = $this->getPropertyProviderService()->getProviderLinkForElement($entity, $element);
                continue;
            }
            catch (NoProviderAvailableException $exception)
            {
                if ($element->usesVocabulary())
                {
                    $elementName =
                        EntityService::PROPERTY_METADATA_SCHEMA . '[' . $schemaInstance->get_schema_id() . '][' .
                        $schemaInstance->get_id() . '][' . $element->get_id() . '][' .
                        EntityService::PROPERTY_METADATA_SCHEMA_EXISTING . ']';

                    $options = [];

                    $elementInstanceVocabularies =
                        $this->getElementService()->getElementInstanceVocabulariesForSchemaInstanceAndElement(
                            $schemaInstance, $element
                        );

                    if (count($elementInstanceVocabularies) == 0)
                    {
                        $elementInstanceVocabularies =
                            $this->getVocabularyService()->getDefaultVocabulariesForUserEntitySchemaInstanceElement(
                                $user, $schemaInstance, $element
                            );
                    }

                    if (count($elementInstanceVocabularies) > 0)
                    {
                        foreach ($elementInstanceVocabularies as $elementInstanceVocabulary)
                        {
                            $item = new stdClass();
                            $item->id = $elementInstanceVocabulary->get_id();
                            $item->value = $elementInstanceVocabulary->get_value();

                            $options[] = $item;
                        }
                    }

                    $elementValue = json_encode($options);
                }
                else
                {
                    $elementName =
                        EntityService::PROPERTY_METADATA_SCHEMA . '[' . $schemaInstance->get_schema_id() . '][' .
                        $schemaInstance . '][' . $element->get_id() . ']';
                    $elementInstanceVocabulary =
                        $this->getElementService()->getElementInstanceVocabularyForSchemaInstanceAndElement(
                            $schemaInstance, $element
                        );

                    if ($elementInstanceVocabulary instanceof Vocabulary && $elementInstanceVocabulary->get_value())
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
        }

        $formValidator->setDefaults($defaults);
    }
}
