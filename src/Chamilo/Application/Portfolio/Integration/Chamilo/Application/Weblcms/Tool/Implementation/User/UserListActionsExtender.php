<?php
namespace Chamilo\Application\Portfolio\Integration\Chamilo\Application\Weblcms\Tool\Implementation\User;

use Chamilo\Application\Portfolio\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\User\Interfaces\UserListActionsExtenderInterface;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\TableCellRenderer;
use Chamilo\Libraries\Translation\Translation;

/**
 * Extends actions for the weblcms user list
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserListActionsExtender implements UserListActionsExtenderInterface
{

    /**
     * Adds actions to the given toolbar for the user list table
     *
     * @param Toolbar $toolbar
     * @param TableCellRenderer $tableCellRenderer
     * @param int $currentUserId
     */
    public function getActions(Toolbar $toolbar, TableCellRenderer $tableCellRenderer, $currentUserId)
    {
        $parameters = [];

        $parameters[Application::PARAM_CONTEXT] = Manager::context();
        $parameters[Application::PARAM_ACTION] = Manager::ACTION_HOME;
        $parameters[Manager::PARAM_USER_ID] = $currentUserId;

        $redirect = new Redirect($parameters);

        $toolbar->add_item(
            new ToolbarItem(
                Translation::getInstance()->getTranslation('ViewPortfolio', [], __NAMESPACE__),
                new NamespaceIdentGlyph('Chamilo\Application\Portfolio'), $redirect->getUrl(),
                ToolbarItem::DISPLAY_ICON, false, null, '_blank'
            )
        );
    }
}