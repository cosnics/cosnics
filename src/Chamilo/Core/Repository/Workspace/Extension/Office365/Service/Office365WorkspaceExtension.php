<?php

namespace Chamilo\Core\Repository\Workspace\Extension\Office365\Service;

use Chamilo\Core\Repository\Component\ExtensionLauncherComponent;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\Extension\Office365\Manager;
use Chamilo\Core\Repository\Workspace\Interfaces\WorkspaceExtensionInterface;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Symfony\Component\Translation\Translator;

/**
 * Extension to support office365 groups in workspaces
 *
 * @package Chamilo\Core\Repository\Workspace\Extension\Office365\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Office365WorkspaceExtension implements WorkspaceExtensionInterface
{
    /**
     * @var WorkspaceOffice365Connector
     */
    protected $workspaceOffice365Connector;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * Office365WorkspaceExtension constructor.
     *
     * @param WorkspaceOffice365Connector $workspaceOffice365Connector
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(WorkspaceOffice365Connector $workspaceOffice365Connector, Translator $translator)
    {
        $this->workspaceOffice365Connector = $workspaceOffice365Connector;
        $this->translator = $translator;
    }

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $workspaceComponent
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspace
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup $workspaceExtensionActions
     */
    public function getWorkspaceActions(
        Application $workspaceComponent, WorkspaceInterface $workspace, User $user,
        ButtonGroup $workspaceExtensionActions
    )
    {
        if(!$workspace instanceof Workspace)
        {
            return;
        }

        $translation = ($this->workspaceOffice365Connector->isOffice365GroupActiveForWorkspace($workspace)) ?
            'VisitOffice365Group' : 'CreateOffice365Group';

        $workspaceExtensionActions->addButton(
            new Button(
                $this->translator->trans($translation, [], Manager::context()), new FontAwesomeGlyph('users'),
                $workspaceComponent->get_url(
                    [
                        Application::PARAM_ACTION => \Chamilo\Core\Repository\Manager::ACTION_EXTENSION_LAUNCHER,
                        ExtensionLauncherComponent::PARAM_EXTENSION_CONTEXT => Manager::context()
                    ]
                ),
                Button::DISPLAY_ICON_AND_LABEL,
                false, null, '_blank'
            )
        );

        if($this->workspaceOffice365Connector->isOffice365GroupActiveForWorkspace($workspace))
        {
            $workspaceExtensionActions->addButton(
                new Button(
                    $this->translator->trans('SyncOffice365Group', [], Manager::context()), new FontAwesomeGlyph('sync'),
                    $workspaceComponent->get_url(
                        [
                            Application::PARAM_ACTION => \Chamilo\Core\Repository\Manager::ACTION_EXTENSION_LAUNCHER,
                            ExtensionLauncherComponent::PARAM_EXTENSION_CONTEXT => Manager::context(),
                            Manager::PARAM_ACTION => Manager::ACTION_SYNC_GROUP
                        ]
                    ),
                    Button::DISPLAY_ICON_AND_LABEL
                )
            );
        }
    }

    /**
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function workspaceDeleted(Workspace $workspace, User $user)
    {
        $this->workspaceOffice365Connector->unlinkOffice365GroupFromWorkspace($workspace, $user);
    }

    /**
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function workspaceUpdated(Workspace $workspace, User $user)
    {
        $this->workspaceOffice365Connector->updateGroupNameForWorkspace($workspace);
    }
}