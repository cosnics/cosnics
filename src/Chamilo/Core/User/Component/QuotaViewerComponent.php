<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\Repository\Quota\Service\StorageSpaceCalculator;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ProgressBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;

/**
 * User manager component which displays the quota to the user.
 * This component displays two progress-bars. The first one
 * displays the used disk space and the second one the number of learning objects in the users user.
 *
 * @package Chamilo\Core\User\Component
 * @author  Bart Mollet
 * @author  Tim De Pauw
 * @author  Hans De Bisschop
 * @author  Dieter De Neef
 */
class QuotaViewerComponent extends Manager
{

    protected ButtonToolBarRenderer $buttonToolBarRenderer;

    private User $selectedUser;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \QuickformException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageUsers');

        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $calculator = $this->getStorageSpaceCalculator();
        $progressBarRenderer = $this->getProgressBarRenderer();
        $filesystemTools = $this->getFilesystemTools();
        $selectedUser = $this->getSelectedUser();

        $progress = $calculator->getStorageSpacePercentageForUser($selectedUser);
        $usedStorageSpaceForUser =
            $filesystemTools->formatFileSize($calculator->getUsedStorageSpaceForUser($this->getSelectedUser()));
        $allowedStorageSpaceForUser =
            $filesystemTools->formatFileSize($calculator->getAllowedStorageSpaceForUser($this->getSelectedUser()));
        $status = $usedStorageSpaceForUser . ' / ' . $allowedStorageSpaceForUser;

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->getButtonToolbarRenderer()->render();
        $html[] =
            '<h3>' . $this->getTranslator()->trans('UsedDiskSpace', [], 'Chamilo\Core\Repository\Quota') . '</h3>';
        $html[] = $progressBarRenderer->renderWithModeBasedOnProgress($progress, $status, null, true);
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    protected function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolBarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    $this->getTranslator()->trans('EditUser', [], Manager::CONTEXT), new FontAwesomeGlyph('pencil-alt'),
                    $this->getUrlGenerator()->fromParameters(
                        [
                            Application::PARAM_CONTEXT => Manager::CONTEXT,
                            Application::PARAM_ACTION => self::ACTION_UPDATE_USER,
                            self::PARAM_USER_USER_ID => $this->getSelectedUser()->getId()
                        ]
                    ), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);

            $this->buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolBarRenderer;
    }

    public function getProgressBarRenderer(): ProgressBarRenderer
    {
        return $this->getService(ProgressBarRenderer::class);
    }

    public function getSelectedUser(): User
    {
        if (!isset($this->selectedUser))
        {
            $selectedUserIdentifier = $this->getRequest()->query->get(self::PARAM_USER_USER_ID);

            if (!$selectedUserIdentifier)
            {
                $this->selectedUser = $this->getUser();
            }
            else
            {
                $this->selectedUser = $this->getUserService()->findUserByIdentifier($selectedUserIdentifier);
            }
        }

        return $this->selectedUser;
    }

    public function getStorageSpaceCalculator(): StorageSpaceCalculator
    {
        return $this->getService(StorageSpaceCalculator::class);
    }
}
