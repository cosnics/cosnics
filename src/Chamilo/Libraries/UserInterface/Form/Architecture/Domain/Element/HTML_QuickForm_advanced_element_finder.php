<?php
namespace Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element;

use Chamilo\Libraries\DependencyInjection\Architecture\Trait\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use HTML_QuickForm_group;
use HTML_QuickForm_hidden;
use HTML_QuickForm_Renderer;
use HTML_QuickForm_select;
use HTML_QuickForm_text;

/**
 * Advanced ajax based element finder.
 * Includes multiple entities, advanced filtering, multiple selects
 *
 * @package Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element
 * @author  Sven Vanpoucke
 */
class HTML_QuickForm_advanced_element_finder extends HTML_QuickForm_group
{
    use DependencyInjectionContainerTrait;

    public const DEFAULT_HEIGHT = 300;
    public const DEFAULT_WIDTH = 292;

    /**
     * An array of configuration values for the elementfinder (eg.
     * max number of selectable items)
     *
     * @var string[]
     */
    private array $configuration;

    private ?AdvancedElementFinderElements $defaultValues = null;

    /**
     * List of types of elements on which can be searched
     */
    private ?AdvancedElementFinderElementTypes $elementTypes;

    private int $height;

    private int $width;

    /**
     * @param string[] $config
     */
    public function __construct(
        ?string $elementName = null, ?string $elementLabel = null,
        ?AdvancedElementFinderElementTypes $elementTypes = null, ?AdvancedElementFinderElements $defaultValues = null,
        ?array $config = []
    )
    {
        parent::__construct($elementName, $elementLabel);

        $this->configuration = $config;
        $this->_type = 'advanced_element_finder';
        $this->_persistantFreeze = true;
        $this->_appendName = false;

        $this->elementTypes = $elementTypes;

        $this->height = self::DEFAULT_HEIGHT;
        $this->width = self::DEFAULT_WIDTH;

        if (!empty($elementTypes)) {
            $this->build_elements();
        }

        $this->setDefaultValues($defaultValues);
    }

    /**
     * Accepts a renderer
     *
     * @param HTML_QuickForm_Renderer $renderer An HTML_QuickForm_Renderer object
     * @param bool $required Whether an element is required
     * @param ?string $error An error message associated with an element
     */
    public function accept(HTML_QuickForm_Renderer $renderer, bool $required = false, ?string $error = null): void
    {
        $renderer->renderElement($this, $required, $error);
    }

    private function build_elements(): void
    {
        $translator = $this->getTranslator();

        $activeHiddenId = 'hidden_active_elements';
        $activateButtonId = 'activate_button';
        $deactivateButtonId = 'deactivate_button';
        $elementTypesSelectBoxId = 'element_types_selector';

        $this->_elements = [];

        $this->_elements[] = new HTML_QuickForm_hidden(
            'active_hidden_' . $this->getName(), '', ['id' => $activeHiddenId]
        );

        $elementTypesArray = [];
        $elementTypesArray[- 1] =
            '-- ' . $translator->trans('SelectElementType', [], StringUtilities::LIBRARIES) . ' --';

        foreach ($this->elementTypes->getTypes() as $elementType) {
            $elementTypesArray[$elementType->getId()] = $elementType->getName();
        }

        $this->_elements[] = new HTML_QuickForm_select(
            'element_types_' . $this->getName(), null, $elementTypesArray,
            ['id' => $elementTypesSelectBoxId, 'class' => 'form-control']
        );

        $safeName = str_replace('[', '_', $this->getName());
        $safeName = str_replace(']', '', $safeName);

        $this->_elements[] = new HTML_QuickForm_text(
            'search_' . $this->getName(), null,
            ['class' => 'element_query form-control', 'id' => $safeName . '_search_field']
        );

        $this->_elements[] = new HTML_QuickForm_stylebutton(
            'activate_' . $this->getName(), $translator->trans('AddToSelection', [], StringUtilities::LIBRARIES),
            ['id' => $activateButtonId, 'class' => 'btn-primary activate_elements form-control'], '',
            new FontAwesomeGlyph('arrow-alt-circle-right', [], null, 'fas')
        );

        $this->_elements[] = new HTML_QuickForm_stylebutton(
            'deactivate_' . $this->getName(), $translator->trans('RemoveFromSelection', [], StringUtilities::LIBRARIES),
            ['id' => $deactivateButtonId, 'class' => 'btn-danger deactivate_elements form-control'], '',
            new FontAwesomeGlyph('arrow-alt-circle-left', [], null, 'fas')
        );
    }

    /**
     * Returns a 'safe' element's value
     *
     * @param array $submitValues array of submitted values to search
     * @param bool $assoc whether to return the value as associative array
     */
    public function exportValue(array &$submitValues, bool $assoc = false): mixed
    {
        return $this->_prepareValue($this->getValue(), $assoc);
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function setHeight(int $height): void
    {
        $this->height = $height;
    }

    public function getValue(): array
    {
        $results = [];
        $values = json_decode($this->_elements[0]->getValue());

        foreach ($values as $value) {
            $splitByUnderscores = explode('_', $value);

            $id = array_pop($splitByUnderscores);
            $type = implode('_', $splitByUnderscores);

            $results[$type][] = $id;
        }

        return $results;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function setWidth(int $width): void
    {
        $this->width = $width;
    }

    public function setDefaultValues(?AdvancedElementFinderElements $defaultValues): void
    {
        if (!$defaultValues) {
            return;
        }

        $this->defaultValues = $defaultValues;

        $defaultIds = [];

        foreach ($defaultValues->getElements() as $defaultValue) {
            $defaultIds[] = $defaultValue->getId();
        }

        $encoded = json_encode($defaultIds);
        $this->_elements[0]->setValue($encoded);
    }

    public function toHtml(): string
    {
        $resourceManager = $this->getResourceManager();
        $webPathBuilder = $this->getWebPathBuilder();

        // Create a safe name for the id (remove array values)
        $safeName = str_replace('[', '_', $this->getName());
        $safeName = str_replace(']', '', $safeName);
        $id = 'tbl_' . $safeName;

        $html = [];

        $html[] = '<div class="element_finder" id="' . $id . '">';

        // Filter row
        $html[] = '<div class="row">';

        $html[] = '<div class="col-md-12">';

        $html[] = $this->_elements[0]->toHtml();

        $html[] = '<div class="element_finder_types">';

        $html[] = '<div class="form-group">';
        $html[] = $this->_elements[1]->toHtml();
        $html[] = '</div>';

        $html[] = '</div>';

        $html[] = '</div>';

        $html[] = '</div>';

        // Search row
        $html[] = '<div class="row">';

        $html[] = '<div class="col-md-12">';

        $html[] = '<div class="element_finder_search form-group">';
        $html[] = '<div class="input-group">';
        $html[] = '<span class="input-group-addon"><span class="fas fa-search"></span></span>';

        $this->_elements[2]->setValue('');
        $html[] = $this->_elements[2]->toHtml();

        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';

        // Elements row
        $html[] = '<div class="row">';

        // Inactive elements
        $html[] = '<div class="col-md-12 col-lg-6">';
        $html[] = '<div class="element_finder_inactive form-group">';
        $html[] =
            '<div id="inactive_elements" class="inactive_elements form-control" style="height: ' . $this->getHeight() .
            'px; overflow: auto;"></div>';
        $html[] = '</div>';

        $html[] = '<div class="element_finder_buttons form-group">';
        $html[] = $this->_elements[3]->toHtml();
        $html[] = '</div>';

        $html[] = '</div>';

        // Active elements
        $html[] = '<div class="col-md-12 col-lg-6">';
        $html[] = '<div class="element_finder_active form-group">';
        $html[] =
            '<div id="active_elements" class="active_elements form-control" style="height: ' . $this->getHeight() .
            'px; overflow: auto;"></div>';
        $html[] = '</div>';

        $html[] = '<div class="element_finder_buttons form-group">';
        $html[] = $this->_elements[4]->toHtml();
        $html[] = '</div>';

        $html[] = '</div>';

        $html[] = '</div>';

        // Make sure everything is within the general div.
        $html[] = '</div>';

        $html[] = $resourceManager->getResourceHtml(
            $webPathBuilder->getJavascriptPath(StringUtilities::LIBRARIES) . 'Jquery/jquery.advelementfinder.min.js'
        );
        $html[] = '<script>';

        if ($this->defaultValues) {
            $defaultValuesText = 'defaultValues: ' . json_encode($this->defaultValues->asArray()) . ', ';
        }
        else {
            $defaultValuesText = '';
        }

        $configurationJson = '';

        foreach ($this->configuration as $name => $value) {
            $configurationJson .= ' ' . $name . ': ' . $value . ', ';
        }

        $configurationJson = substr($configurationJson, 0, strlen($configurationJson) - 2);

        $html[] =
            '$("#' . $id . '").advelementfinder({ name: "' . $safeName . '", ' . $defaultValuesText . 'elementTypes: ' .
            json_encode($this->elementTypes->asArray()) . ',' . $configurationJson . '});';

        $html[] = '</script>';

        return implode(PHP_EOL, $html);
    }
}
