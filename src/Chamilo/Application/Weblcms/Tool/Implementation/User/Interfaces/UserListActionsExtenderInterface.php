<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Interfaces;

use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Table\TableCellRenderer;

/**
 * Interface for user list actions extender
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface UserListActionsExtenderInterface
{

    /**
     * Adds actions to the given toolbar for the user list table
     * 
     * @param Toolbar $toolbar
     * @param TableCellRenderer $tableCellRenderer
     * @param int $currentUserId
     */
    public function getActions(Toolbar $toolbar, TableCellRenderer $tableCellRenderer, $currentUserId);
}