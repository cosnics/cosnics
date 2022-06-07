<?php
namespace Chamilo\Core\Repository\Common\Import;

use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Core\Repository\Common\Import
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ImportTypeSelector
{

    /**
     *
     * @var string[]
     */
    private $parameters;

    /**
     *
     * @var string[]
     */
    private $allowedContentObjectTypes;

    /**
     *
     * @param string[] $parameters
     * @param string[] $allowedContentObjectTypes
     */
    public function __construct($parameters, $allowedContentObjectTypes = [])
    {
        $this->parameters = $parameters;
        $this->allowedContentObjectTypes = $allowedContentObjectTypes;
    }

    /**
     *
     * @return string[]
     */
    public function getAllowedContentObjectTypes()
    {
        return $this->allowedContentObjectTypes;
    }

    /**
     *
     * @param string[] $allowedContentObjectTypes
     */
    public function setAllowedContentObjectTypes($allowedContentObjectTypes)
    {
        $this->allowedContentObjectTypes = $allowedContentObjectTypes;
    }

    /**
     * @return string[][]
     */
    public function getImportTypes()
    {
        $importTypes = [];

        foreach ($this->getAllowedContentObjectTypes() as $type)
        {
            $objectImportTypes = ContentObjectImportImplementation::get_types_for_object(
                ClassnameUtilities::getInstance()->getNamespaceParent($type, 3)
            );

            foreach ($objectImportTypes as $objectImportType)
            {
                if (!array_key_exists($objectImportType, $importTypes))
                {
                    $importTypeName =
                        (string) StringUtilities::getInstance()->createString($objectImportType)->upperCamelize();

                    /**
                     * @var \Chamilo\Core\Repository\Common\Import\ContentObjectImportController $class
                     */
                    $class = __NAMESPACE__ . '\\' . $importTypeName . '\\' . $importTypeName .
                        'ContentObjectImportController';

                    if (class_exists($class) && $class::is_available())
                    {
                        $importTypes[$objectImportType] = array(
                            'label' => Translation::get(
                                'ImportType' . $importTypeName, null, Manager::context()
                            ),
                            'namespace' => 'Chamilo\Core\Repository\Import\\' . $importTypeName
                        );
                    }
                }
            }
        }

        natcasesort($importTypes);

        return $importTypes;
    }

    /**
     * @param string $importType
     *
     * @return string
     */
    public function getLink($importType)
    {
        $parameters = $this->getParameters();
        $parameters[ContentObjectImportService::PARAM_IMPORT_TYPE] = $importType;

        $importUrl = new Redirect($parameters);

        return $importUrl->getUrl();
    }

    /**
     *
     * @return string[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     *
     * @param string[] $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton
     */
    public function getTypeSelectorDropdownButton()
    {
        $dropdownButton = new DropdownButton(
            Translation::get('Import', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('download'),
            Button::DISPLAY_ICON_AND_LABEL
        );

        $dropdownButton->addSubButtons($this->getTypeSelectorSubButtons());

        return $dropdownButton;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[]
     */
    public function getTypeSelectorSubButtons()
    {
        $subButtons = [];

        foreach ($this->getImportTypes() as $type => $properties)
        {
            $glyph = new NamespaceIdentGlyph(
                $properties['namespace'], true, false, false, IdentGlyph::SIZE_MINI, array('fa-fw')
            );

            $subButtons[] = new SubButton($properties['label'], $glyph, $this->getLink($type));
        }

        return $subButtons;
    }

    /**
     *
     * @return string
     */
    public function renderTypeSelector()
    {
        $html = [];

        $html[] = '<div class="btn-group">';

        foreach ($this->getImportTypes() as $type => $properties)
        {
            $html[] = '<a class="btn btn-default" href="' . $this->getLink($type) . '">';

            $glyph = new NamespaceIdentGlyph(
                $properties['namespace'], true, false, false, IdentGlyph::SIZE_BIG, array('fa-fw')
            );

            $html[] = $glyph->render();
            $html[] = $properties['label'];

            $html[] = '</a>';
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}