<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\Repository\Quota\Service\StorageSpaceCalculator;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package user.lib.user_manager.component
 */

/**
 * User manager component which displays the quota to the user.
 * This component displays two progress-bars. The first one
 * displays the used disk space and the second one the number of learning objects in the users user.
 *
 * @author Bart Mollet
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class QuotaViewerComponent extends Manager
{

    private $selected_user;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageUsers');

        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $selected_user_id = $this->getRequest()->query->get(self::PARAM_USER_USER_ID);
        if (!$selected_user_id)
        {
            $this->selected_user = $this->getUser();
        }
        else
        {
            $this->selected_user = $this->getUserService()->findUserByIdentifier($selected_user_id);
        }

        $calculator = $this->getStorageSpaceCalculator();

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->getButtonToolbarRenderer();

        $filesystemTools = $this->getFilesystemTools();

        $html[] = '<h3>' . htmlentities(Translation::get('UsedDiskSpace')) . '</h3>';
        $html[] = Calculator::getBar(
            $calculator->getStorageSpacePercentageForUser($this->selected_user),
            $filesystemTools->formatFileSize($calculator->getUsedStorageSpaceForUser($this->selected_user)) . ' / ' .
            $filesystemTools->formatFileSize(
                $calculator->getAllowedStorageSpaceForUser($this->selected_user)
            )
        );
        $html[] = '<div style="clear: both;">&nbsp;</div>';

        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    private function getButtonToolbarRenderer()
    {
        $buttonToolbar = new ButtonToolBar();
        $commonActions = new ButtonGroup();

        $commonActions->addButton(
            new Button(
                Translation::get('EditUser'), new FontAwesomeGlyph('pencil-alt'), $this->get_url(
                [
                    Application::PARAM_ACTION => self::ACTION_UPDATE_USER,
                    self::PARAM_USER_USER_ID => $this->selected_user->get_id()
                ]
            ), ToolbarItem::DISPLAY_ICON_AND_LABEL
            )
        );

        $buttonToolbar->addButtonGroup($commonActions);

        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        $buttonToolbarRenderer->render();
    }

    public function getStorageSpaceCalculator(): StorageSpaceCalculator
    {
        return $this->getService(StorageSpaceCalculator::class);
    }
}
