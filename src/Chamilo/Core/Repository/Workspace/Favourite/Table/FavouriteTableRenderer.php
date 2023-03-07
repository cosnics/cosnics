<?php
namespace Chamilo\Core\Repository\Workspace\Favourite\Table;

use Chamilo\Core\Repository\Workspace\Favourite\Manager;
use Chamilo\Core\Repository\Workspace\Table\WorkspaceTableRenderer;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Repository\Workspace\Favourite\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FavouriteTableRenderer extends WorkspaceTableRenderer
{
    public function getTableActions(): TableActions
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $deleteUrl = $urlGenerator->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Manager::PARAM_ACTION => Manager::ACTION_DELETE
            ]
        );

        $actions->addAction(
            new TableAction(
                $deleteUrl, $translator->trans('DeleteSelected', [], StringUtilities::LIBRARIES), true
            )
        );

        return $actions;
    }
}
