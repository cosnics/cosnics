<?php
namespace Chamilo\Core\Repository\Viewer;

use Chamilo\Core\Repository\Common\Import\ImportTypeSelector;
use Chamilo\Core\Repository\Selector\Renderer\SubButtonTypeSelectorRenderer;
use Chamilo\Core\Repository\Selector\TypeSelectorFactory;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonDivider;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonHeader;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Core\Repository\Viewer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ActionSelector
{
    use DependencyInjectionContainerTrait;

    /**
     * @var string[]
     */
    protected $allowedContentObjectTypes;

    /**
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    protected $application;

    /**
     * @var string[]
     */
    protected $classes;

    /**
     * @var \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[]
     */
    protected $extraActions;

    /**
     * @var string[]
     */
    protected $parameters;

    /**
     * @var \Chamilo\Core\Repository\Selector\TypeSelector
     */
    protected $typeSelector;

    /**
     * @var int
     */
    protected $userIdentifier;

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param int $userIdentifier
     * @param string[] $allowedContentObjectTypes
     * @param string[] $parameters
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[] extraActions
     * @param string[] $classes
     *
     * @throws \Exception
     */
    public function __construct(
        Application $application, $userIdentifier, $allowedContentObjectTypes = [], $parameters = [],
        $extraActions = [], array $classes = []
    )
    {
        $this->initializeContainer();

        $this->application = $application;
        $this->userIdentifier = $userIdentifier;
        $this->allowedContentObjectTypes = $allowedContentObjectTypes;
        $this->parameters = $parameters;
        $this->extraActions = $extraActions;
        $this->classes = $classes;
    }

    /**
     * @param string $label
     * @param \Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph|string $image
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton
     */
    public function getActionButton($label, $image): DropdownButton
    {
        $dropdownButton = $this->getDropdownButton($label, $image);

        if ($this->hasExtraActions())
        {
            $dropdownButton->addSubButtons($this->getExtraActions());
            $dropdownButton->addSubButton(new SubButtonDivider());
        }

        $typeSelector = $this->getTypeSelector();

        if ($typeSelector->count_options() > 1)
        {
            $dropdownButton->addSubButton(new SubButtonHeader(Translation::get('CreatorTitle')));
            $dropdownButton->addSubButtons($this->getCreationOptions());
        }

        // Browser + Shared objects
        if ($this->getApplication()->isAuthorized(\Chamilo\Core\Repository\Manager::CONTEXT))
        {
            if ($typeSelector->count_options() > 1)
            {
                $dropdownButton->addSubButton(new SubButtonDivider());
            }

            $dropdownButton->addSubButtons($this->getExistingOptions());
            $dropdownButton->addSubButton(new SubButtonDivider());

            // Import options
            $dropdownButton->addSubButton(new SubButtonHeader(Translation::get('ImporterTitle')));
            $dropdownButton->addSubButtons($this->getImportOptions());
        }

        return $dropdownButton;
    }

    /**
     * @return string[] $allowedContentObjectTypes
     */
    public function getAllowedContentObjectTypes()
    {
        return $this->allowedContentObjectTypes;
    }

    /**
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return string[]
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[]
     */
    public function getCreationOptions()
    {
        return $this->getSubButtonTypeSelectorRenderer()->render();
    }

    /**
     * Returns the dropdown button
     *
     * @param string $label
     * @param mixed $image
     *
     * @return DropdownButton|SplitDropdownButton
     */
    public function getDropdownButton($label, $image)
    {
        $typeSelector = $this->getTypeSelector();

        if ($typeSelector->count_options() == 1)
        {
            return $this->getSingleCreationOptionDropdownButton($label, $image);
        }

        return $this->getMultipleCreationOptionsDropdownButton($label, $image);
    }

    /**
     * @param string $action
     *
     * @return string
     */
    public function getExistingLink($action, $inWorkspace = false)
    {
        $parameters = $this->getParameters();
        $parameters[Manager::PARAM_ACTION] = $action;

        if ($inWorkspace)
        {
            $parameters[Manager::PARAM_IN_WORKSPACES] = 1;
        }

        return $this->getUrlGenerator()->fromParameters($parameters);
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[]
     */
    public function getExistingOptions()
    {
        $subButtons = [];

        $subButtons[] = new SubButtonHeader(Translation::get('SelectFrom'));

        $subButtons[] = new SubButton(
            Translation::get('SelectFromRepository'), new FontAwesomeGlyph('folder', [], null, 'fas'),
            $this->getExistingLink(Manager::ACTION_BROWSER), SubButton::DISPLAY_ICON_AND_LABEL
        );

        $validWorkspaces = $this->getWorkspaceService()->countWorkspacesForUser(
            $this->application->getUser(), RightsService::RIGHT_USE
        );

        if ($validWorkspaces > 0)
        {
            $subButtons[] = new SubButton(
                Translation::get('SelectFromWorkspaces'), new FontAwesomeGlyph('users', [], null, 'fas'),
                $this->getExistingLink(Manager::ACTION_BROWSER, true)
            );
        }

        return $subButtons;
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[]
     */
    public function getExtraActions()
    {
        return $this->extraActions;
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[]
     */
    public function getImportOptions()
    {
        $importParameters = $this->getParameters();
        $importParameters[Manager::PARAM_ACTION] = Manager::ACTION_IMPORTER;

        $importTypeSelector = new ImportTypeSelector($importParameters, $this->getAllowedContentObjectTypes());

        return $importTypeSelector->getTypeSelectorSubButtons();
    }

    /**
     * Returns the dropdown button without a default option
     *
     * @param $label
     * @param $image
     *
     * @return DropdownButton
     */
    protected function getMultipleCreationOptionsDropdownButton($label, $image)
    {
        return new DropdownButton(
            $label, $image, SplitDropdownButton::DISPLAY_ICON_AND_LABEL, $this->getClasses()
        );
    }

    /**
     * @return string[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Returns the dropdown button with the first creation option as default option
     *
     * @param $label
     * @param $image
     *
     * @return SplitDropdownButton
     */
    protected function getSingleCreationOptionDropdownButton($label, $image)
    {
        return new SplitDropdownButton(
            $label, $image, $this->getSingleCreationOptionUrl(), SplitDropdownButton::DISPLAY_ICON_AND_LABEL, false,
            $this->getClasses()
        );
    }

    /**
     * @return string
     */
    public function getSingleCreationOptionUrl()
    {
        $typeSelector = $this->getTypeSelector();

        $templateIds = $typeSelector->get_unique_content_object_template_ids();
        $templateId = array_shift($templateIds);

        return $this->getSubButtonTypeSelectorRenderer()->getContentObjectTypeUrl($templateId);
    }

    /**
     * @return \Chamilo\Core\Repository\Selector\Renderer\SubButtonTypeSelectorRenderer
     */
    public function getSubButtonTypeSelectorRenderer()
    {
        if (!isset($this->subButtonTypeSelectorRenderer))
        {
            $typeSelector = $this->getTypeSelector();
            $createParameters = $this->getParameters();
            $createParameters[Manager::PARAM_ACTION] = Manager::ACTION_CREATOR;

            $this->subButtonTypeSelectorRenderer = new SubButtonTypeSelectorRenderer(
                $this->getApplication(), $typeSelector, $createParameters
            );
        }

        return $this->subButtonTypeSelectorRenderer;
    }

    /**
     * @return \Chamilo\Core\Repository\Selector\TypeSelector
     */
    public function getTypeSelector()
    {
        if (!isset($this->typeSelector))
        {
            $typeSelectorFactory = new TypeSelectorFactory(
                $this->getAllowedContentObjectTypes(), $this->getUserIdentifier(), TypeSelectorFactory::MODE_FLAT_LIST
            );
            $this->typeSelector = $typeSelectorFactory->getTypeSelector();
        }

        return $this->typeSelector;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(UrlGenerator::class);
    }

    /**
     * @return int
     */
    public function getUserIdentifier()
    {
        return $this->userIdentifier;
    }

    public function getWorkspaceService(): WorkspaceService
    {
        return $this->getService(WorkspaceService::class);
    }

    /**
     * @return bool
     */
    public function hasExtraActions()
    {
        $extraActions = $this->getExtraActions();

        return count($extraActions) > 0;
    }

    /**
     * @param string[] $allowedContentObjectTypes
     */
    public function setAllowedContentObjectTypes($allowedContentObjectTypes = [])
    {
        $this->allowedContentObjectTypes = $allowedContentObjectTypes;
    }

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @param string[] $classes
     */
    public function setClasses(array $classes)
    {
        $this->classes = $classes;
    }

    /**
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\SubButton[] $extraActions
     */
    public function setExtraActions($extraActions)
    {
        $this->extraActions = $extraActions;
    }

    /**
     * @param string[] $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @param int $userIdentifier
     */
    public function setUserIdentifier($userIdentifier)
    {
        $this->userIdentifier = $userIdentifier;
    }
}