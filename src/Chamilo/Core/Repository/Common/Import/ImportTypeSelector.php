<?php
namespace Chamilo\Core\Repository\Common\Import;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

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
    public function __construct($parameters, $allowedContentObjectTypes = array())
    {
        $this->parameters = $parameters;
        $this->allowedContentObjectTypes = $allowedContentObjectTypes;
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
     *
     * @return string
     */
    public function renderTypeSelector()
    {
        $html = array();

        $html[] = '<div class="btn-group">';

        foreach ($this->getAllowedContentObjectTypes() as $type => $name)
        {
            $typeImageName = (string) StringUtilities::getInstance()->createString($type)->upperCamelize();
            $imageContext = \Chamilo\Core\Repository\Manager::package();

            $html[] = '<a class="btn btn-default" href="' . $this->getLink($type) . '">';
            $html[] = '<img src="' . Theme::getInstance()->getImagePath($imageContext, 'Import/' . $typeImageName) .
                 '" /> ';
            $html[] = $name;
            $html[] = '</a>';
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getLink($importType)
    {
        $parameters = $this->getParameters();
        $parameters[ContentObjectImportService::PARAM_IMPORT_TYPE] = $importType;

        $importUrl = new Redirect($parameters);
        return $importUrl->getUrl();
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton
     */
    public function getTypeSelectorDropdownButton()
    {
        $dropdownButton = new DropdownButton(
            Translation::get('Import', null, Utilities::COMMON_LIBRARIES),
            new FontAwesomeGlyph('download'),
            Button::DISPLAY_ICON_AND_LABEL);

        $dropdownButton->addSubButtons($this->getTypeSelectorSubButtons());

        return $dropdownButton;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[]
     */
    public function getTypeSelectorSubButtons()
    {
        $subButtons = array();

        foreach ($this->getImportTypes() as $type => $name)
        {
            $typeImageName = (string) StringUtilities::getInstance()->createString($type)->upperCamelize();
            $imageContext = \Chamilo\Core\Repository\Manager::package();

            $subButtons[] = new SubButton(
                $name,
                Theme::getInstance()->getImagePath($imageContext, 'Import/16/' . $typeImageName),
                $this->getLink($type));
        }

        return $subButtons;
    }

    public function getImportTypes()
    {
        $importTypes = array();

        foreach ($this->getAllowedContentObjectTypes() as $type)
        {
            $objectImportTypes = ContentObjectImportImplementation::get_types_for_object(
                ClassnameUtilities::getInstance()->getNamespaceParent($type, 3));

            foreach ($objectImportTypes as $objectImportType)
            {
                if (! array_key_exists($objectImportType, $importTypes))
                {
                    $importTypeName = (string) StringUtilities::getInstance()->createString($objectImportType)->upperCamelize();

                    $class = __NAMESPACE__ . '\\' . $importTypeName . '\\' . $importTypeName .
                         'ContentObjectImportController';

                    if (class_exists($class) && $class::is_available())
                    {
                        $importTypes[$objectImportType] = Translation::get(
                            'ImportType' . $importTypeName,
                            null,
                            \Chamilo\Core\Repository\Manager::context());
                    }
                }
            }
        }

        natcasesort($importTypes);

        return $importTypes;
    }
}