<?php
namespace Chamilo\Core\Repository\Viewer;

use Chamilo\Core\Repository\Common\Import\ImportTypeSelector;
use Chamilo\Core\Repository\Selector\Renderer\SubButtonTypeSelectorRenderer;
use Chamilo\Core\Repository\Selector\TypeSelectorFactory;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonDivider;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonHeader;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;

/**
 *
 * @package Chamilo\Core\Repository\Viewer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ActionSelector
{

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

    /**
     *
     * @var integer
     */
    private $userIdentifier;

    /**
     *
     * @var string[]
     */
    private $allowedContentObjectTypes;

    /**
     *
     * @var string[]
     */
    private $parameters;

    /**
     *
     * @var \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[]
     */
    private $extraActions;

    /**
     *
     * @var \Chamilo\Core\Repository\Selector\TypeSelector
     */
    private $typeSelector;

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param integer $userIdentifier
     * @param string[] $allowedContentObjectTypes
     * @param string[] $parameters
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[] extraActions
     */
    public function __construct(Application $application, $userIdentifier, $allowedContentObjectTypes = array(),
        $parameters = array(), $extraActions = array())
    {
        $this->application = $application;
        $this->userIdentifier = $userIdentifier;
        $this->allowedContentObjectTypes = $allowedContentObjectTypes;
        $this->parameters = $parameters;
        $this->extraActions = $extraActions;
    }

    /**
     *
     * @return string[] $allowedContentObjectTypes
     */
    public function getAllowedContentObjectTypes()
    {
        return $this->allowedContentObjectTypes;
    }

    /**
     *
     * @param string[] $allowedContentObjectTypes
     */
    public function setAllowedContentObjectTypes($allowedContentObjectTypes = array())
    {
        $this->allowedContentObjectTypes = $allowedContentObjectTypes;
    }

    /**
     *
     * @return integer
     */
    public function getUserIdentifier()
    {
        return $this->userIdentifier;
    }

    /**
     *
     * @param integer $userIdentifier
     */
    public function setUserIdentifier($userIdentifier)
    {
        $this->userIdentifier = $userIdentifier;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;
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
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[]
     */
    public function getExtraActions()
    {
        return $this->extraActions;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[] $extraActions
     */
    public function setExtraActions($extraActions)
    {
        $this->extraActions = $extraActions;
    }

    /**
     *
     * @return boolean
     */
    public function hasExtraActions()
    {
        $extraActions = $this->getExtraActions();
        return count($extraActions) > 0;
    }

    /**
     *
     * @param string $label
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\InlineGlyph|string $image
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton
     */
    public function getActionButton($label, $image)
    {
        $dropdownButton = $this->getDropdownButton($label, $image);

        if ($this->hasExtraActions())
        {
            $dropdownButton->addSubButtons($this->getExtraActions());
            $dropdownButton->addSubButton(new SubButtonDivider());
        }

        if ($this->getTypeSelector()->count_options() > 1)
        {
            $dropdownButton->addSubButton(new SubButtonHeader(Translation :: get('CreatorTitle')));
            $dropdownButton->addSubButtons($this->getCreationOptions());
            $dropdownButton->addSubButton(new SubButtonDivider());
        }

        // Browser + Shared objects
        $dropdownButton->addSubButtons($this->getExistingOptions());
        $dropdownButton->addSubButton(new SubButtonDivider());

        // Import options
        $dropdownButton->addSubButton(new SubButtonHeader(Translation :: get('ImporterTitle')));
        $dropdownButton->addSubButtons($this->getImportOptions());

        return $dropdownButton;
    }

    public function getDropdownButton($label, $image)
    {
        $typeSelector = $this->getTypeSelector();

        if ($typeSelector->count_options() == 1)
        {
            $dropdownButton = new SplitDropdownButton($label, $image, $this->getSingleCreationOptionUrl());
        }
        else
        {
            $dropdownButton = new DropdownButton($label, $image);
        }

        return $dropdownButton;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[]
     */
    public function getCreationOptions()
    {
        return $this->getSubButtonTypeSelectorRenderer()->render();
    }

    /**
     *
     * @return string
     */
    public function getSingleCreationOptionUrl()
    {
        $typeSelector = $this->getTypeSelector();

        $templateIds = $typeSelector->get_unique_content_object_template_ids();
        $templateId = array_pop($templateIds);

        return $this->getSubButtonTypeSelectorRenderer()->getContentObjectTypeUrl($templateId);
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Selector\Renderer\SubButtonTypeSelectorRenderer
     */
    public function getSubButtonTypeSelectorRenderer()
    {
        if (! isset($this->subButtonTypeSelectorRenderer))
        {
            $typeSelector = $this->getTypeSelector();
            $createParameters = $this->getParameters();
            $createParameters[Manager :: PARAM_ACTION] = Manager :: ACTION_CREATOR;

            $this->subButtonTypeSelectorRenderer = new SubButtonTypeSelectorRenderer(
                $this->getApplication(),
                $typeSelector,
                $createParameters);
        }

        return $this->subButtonTypeSelectorRenderer;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[]
     */
    public function getImportOptions()
    {
        $importParameters = $this->getParameters();
        $importParameters[Manager :: PARAM_ACTION] = Manager :: ACTION_IMPORTER;

        $importTypeSelector = new ImportTypeSelector($importParameters, $this->getAllowedContentObjectTypes());

        return $importTypeSelector->getTypeSelectorSubButtons();
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[]
     */
    public function getExistingOptions()
    {
        $subButtons = array();

        $subButtons[] = new SubButtonHeader(Translation :: get('SelectFrom'));

        $subButtons[] = new SubButton(
            Translation :: get('SelectFromRepository'),
            Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Action/Browser'),
            $this->getExistingLink(Manager :: ACTION_BROWSER),
            SubButton :: DISPLAY_ICON_AND_LABEL);

        $subButtons[] = new SubButton(
            Translation :: get('SelectFromWorkspaces'),
            Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Action/Share'),
            $this->getExistingLink(Manager :: ACTION_BROWSER));

        return $subButtons;
    }

    /**
     *
     * @param string $action
     * @return string
     */
    public function getExistingLink($action)
    {
        $parameters = $this->getParameters();
        $parameters[Manager :: PARAM_ACTION] = $action;

        $existingLink = new Redirect($parameters);

        return $existingLink->getUrl();
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Selector\TypeSelector
     */
    public function getTypeSelector()
    {
        if (! isset($this->typeSelector))
        {
            $typeSelectorFactory = new TypeSelectorFactory(
                $this->getAllowedContentObjectTypes(),
                $this->getUserIdentifier());
            $this->typeSelector = $typeSelectorFactory->getTypeSelector();
        }

        return $this->typeSelector;
    }
}