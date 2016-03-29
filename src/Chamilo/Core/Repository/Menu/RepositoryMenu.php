<?php
namespace Chamilo\Core\Repository\Menu;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Format\Structure\ActionBar\BootstrapGlyph;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

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

    /**
     *
     * @return string
     */
    public function render()
    {
        $repositoryManager = $this->getRepositoryManager();
        $buttonToolBar = new ButtonToolBar();
        $buttonToolBar->addClass('btn-action-toolbar-vertical');

        $rightsService = RightsService :: getInstance();
        $canAddContentObjects = $rightsService->canAddContentObjects(
            $repositoryManager->get_user(),
            $repositoryManager->getWorkspace());

        if ($canAddContentObjects)
        {
            $buttonGroup = new ButtonGroup(array(), array('btn-group-vertical'));

            $buttonGroup->addButton(
                new Button(
                    Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES),
                    new BootstrapGlyph('plus'),
                    $repositoryManager->get_url(
                        array(
                            \Chamilo\Core\Repository\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Manager :: ACTION_CREATE_CONTENT_OBJECTS)),
                    Button :: DISPLAY_ICON_AND_LABEL,
                    false,
                    'btn-primary'));

            $buttonGroup->addButton(
                new Button(
                    Translation :: get('Import', null, Utilities :: COMMON_LIBRARIES),
                    new BootstrapGlyph('import'),
                    $repositoryManager->get_url(
                        array(
                            \Chamilo\Core\Repository\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Manager :: ACTION_IMPORT_CONTENT_OBJECTS))));

            if (! $repositoryManager->getWorkspace() instanceof PersonalWorkspace)
            {
                $buttonGroup->addButton(
                    new Button(
                        Translation :: get('AddExisting'),
                        new BootstrapGlyph('plus'),
                        $repositoryManager->get_url(
                            array(
                                \Chamilo\Core\Repository\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Manager :: ACTION_WORKSPACE,
                                \Chamilo\Core\Repository\Workspace\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Manager :: ACTION_PUBLISH)),
                        Button :: DISPLAY_ICON_AND_LABEL,
                        false,
                        'btn-primary'));
            }

            $buttonToolBar->addButtonGroup($buttonGroup);
        }

        $buttonGroup = new ButtonGroup(array(), array('btn-group-vertical'));

        if ($repositoryManager->getWorkspace() instanceof PersonalWorkspace)
        {
            $buttonGroup->addButton(
                new Button(
                    Translation :: get('MyPublications'),
                    new BootstrapGlyph('list'),
                    $repositoryManager->get_url(
                        array(
                            \Chamilo\Core\Repository\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Manager :: ACTION_PUBLICATION),
                        array(\Chamilo\Core\Repository\Publication\Manager :: PARAM_ACTION))));
        }

        if ($repositoryManager->getWorkspace() instanceof PersonalWorkspace)
        {
            $buttonGroup->addButton(
                new Button(
                    Translation :: get('Quota'),
                    new BootstrapGlyph('stats'),
                    $repositoryManager->get_url(
                        array(
                            \Chamilo\Core\Repository\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Manager :: ACTION_QUOTA,
                            \Chamilo\Core\Repository\Manager :: PARAM_CATEGORY_ID => null,
                            \Chamilo\Core\Repository\Quota\Manager :: PARAM_ACTION => null,
                            DynamicTabsRenderer :: PARAM_SELECTED_TAB => null))));

            $buttonGroup->addButton(
                new Button(
                    Translation :: get('ViewDoubles'),
                    new BootstrapGlyph('duplicate'),
                    $repositoryManager->get_url(
                        array(
                            \Chamilo\Core\Repository\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Manager :: ACTION_VIEW_DOUBLES))));

            $buttonGroup->addButton(
                new Button(
                    Translation :: get('RecycleBin'),
                    new BootstrapGlyph('trash'),
                    $repositoryManager->get_recycle_bin_url()));
        }

        $buttonToolBar->addButtonGroup($buttonGroup);
        $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolBarRenderer->render();
    }
}
