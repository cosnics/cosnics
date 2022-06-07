<?php

namespace Chamilo\Core\Repository\Menu;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\Common\Import\ImportTypeSelector;
use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Core\Repository\Menu
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RepositoryMenu
{

    /**
     *
     * @var \Chamilo\Core\Repository\Manager
     */
    private $repositoryManager;

    /**
     *
     * @param \Chamilo\Core\Repository\Manager $repositoryManager
     */
    public function __construct(Manager $repositoryManager)
    {
        $this->repositoryManager = $repositoryManager;
    }

    /**
     *
     * @return string
     */
    public function render()
    {
        $repositoryManager = $this->getRepositoryManager();
        $buttonToolBar = new ButtonToolBar();
        $buttonToolBar->addClass('btn-action-toolbar-vertical');

        $rightsService = RightsService::getInstance();
        $canAddContentObjects = $rightsService->canAddContentObjects(
            $repositoryManager->get_user(), $repositoryManager->getWorkspace()
        );

        if ($canAddContentObjects)
        {
            $buttonGroup = new ButtonGroup([], array('btn-group-vertical'));

            $buttonGroup->addButton(
                new Button(
                    Translation::get('Create', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('plus'),
                    $repositoryManager->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_CREATE_CONTENT_OBJECTS
                        ), array(Manager::PARAM_IMPORT_TYPE)
                    ), Button::DISPLAY_ICON_AND_LABEL, false, 'btn-primary'
                )
            );

            $importParameters = $repositoryManager->get_parameters();
            $importParameters[Manager::PARAM_ACTION] = Manager::ACTION_IMPORT_CONTENT_OBJECTS;

            $importTypeSelector = new ImportTypeSelector($importParameters, $this->getImportTypes());
            $buttonGroup->addButton($importTypeSelector->getTypeSelectorDropdownButton());

            if (!$repositoryManager->getWorkspace() instanceof PersonalWorkspace)
            {
                $buttonGroup->addButton(
                    new Button(
                        Translation::get('AddExisting'), new FontAwesomeGlyph('hdd', [], null, 'far'),
                        $repositoryManager->get_url(
                            array(

                                Manager::PARAM_ACTION => Manager::ACTION_WORKSPACE,
                                \Chamilo\Core\Repository\Workspace\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Manager::ACTION_PUBLISH,
                                FilterData::FILTER_CATEGORY => FilterData::getInstance(
                                    $repositoryManager->getWorkspace()
                                )->get_category()
                            ), array(Manager::PARAM_IMPORT_TYPE)
                        ), Button::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            $buttonToolBar->addButtonGroup($buttonGroup);
        }

        $buttonGroup = new ButtonGroup([], array('btn-group-vertical'));

        if ($repositoryManager->getWorkspace() instanceof PersonalWorkspace)
        {
            $buttonGroup->addButton(
                new Button(
                    Translation::get('MyPublications'), new FontAwesomeGlyph('list'), $repositoryManager->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_PUBLICATION
                    ), array(\Chamilo\Core\Repository\Publication\Manager::PARAM_ACTION, Manager::PARAM_IMPORT_TYPE)
                )
                )
            );
        }

        if ($repositoryManager->getWorkspace() instanceof PersonalWorkspace)
        {
            $buttonGroup->addButton(
                new Button(
                    Translation::get('Quota'), new FontAwesomeGlyph('chart-bar'), $repositoryManager->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_QUOTA,
                        Manager::PARAM_CATEGORY_ID => null,
                        \Chamilo\Core\Repository\Quota\Manager::PARAM_ACTION => null,
                        DynamicTabsRenderer::PARAM_SELECTED_TAB => null
                    ), array(Manager::PARAM_IMPORT_TYPE)
                )
                )
            );

            $buttonGroup->addButton(
                new Button(
                    Translation::get('ViewDoubles'), new FontAwesomeGlyph('copy'), $repositoryManager->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_VIEW_DOUBLES
                    ), array(Manager::PARAM_IMPORT_TYPE)
                )
                )
            );

            $buttonGroup->addButton(
                new Button(
                    Translation::get('RecycleBin'), new FontAwesomeGlyph('trash-alt'),
                    $repositoryManager->get_recycle_bin_url()
                )
            );
        }

        $buttonToolBar->addButtonGroup($buttonGroup);

        $extensionsButtonGroup = new ButtonGroup([], array('btn-group-vertical'));
        $buttonToolBar->addButtonGroup($extensionsButtonGroup);

        $this->getRepositoryManager()->getWorkspaceExtensionManager()->getWorkspaceActions(
            $this->getRepositoryManager(), $repositoryManager->getWorkspace(), $repositoryManager->getUser(),
            $extensionsButtonGroup
        );

        $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolBarRenderer->render();
    }

    /**
     *
     * @return string[]
     */
    public function getImportTypes()
    {
        $registrations = Configuration::getInstance()->get_registrations_by_type(
            'Chamilo\Core\Repository\ContentObject'
        );

        $types = [];

        foreach ($registrations as $registration)
        {
            $namespace = $registration[Registration::PROPERTY_CONTEXT];
            $packageName = ClassnameUtilities::getInstance()->getPackageNameFromNamespace($namespace);
            $types[] = $namespace . '\Storage\DataClass\\' . $packageName;
        }

        return $types;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Manager
     */
    public function getRepositoryManager()
    {
        return $this->repositoryManager;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Manager $repositoryManager
     */
    public function setRepositoryManager(Manager $repositoryManager)
    {
        $this->repositoryManager = $repositoryManager;
    }
}
