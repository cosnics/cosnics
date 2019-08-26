<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Component\SubSubscribedGroup;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Table;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * * *************************************************************************** Cell renderer for a course subgroup
 * browser table.
 * 
 * @author Stijn Van Hoecke ****************************************************************************
 */
class SubSubscribedPlatformGroupTableCellRenderer extends DataClassTableCellRenderer
{

    public function render_cell($column, $group)
    {
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            
            case Group::PROPERTY_NAME :
                $title = parent::render_cell($column, $group);
                $title_short = $title;
                if (strlen($title_short) > 53)
                {
                    $title_short = mb_substr($title_short, 0, 50) . '&hellip;';
                }
                return $title_short;
            
            case Group::PROPERTY_DESCRIPTION :
                $description = strip_tags(parent::render_cell($column, $group));
                return StringUtilities::getInstance()->truncate($description);
            case Translation::get(
                SubSubscribedPlatformGroupTableColumnModel::USERS, 
                null, 
                \Chamilo\Core\User\Manager::context()) :
                return $this->getTable()->getGroupSubscriptionService()->countUsersDirectlySubscribedToGroup($group);
            case Translation::get(SubSubscribedPlatformGroupTableColumnModel::SUBGROUPS) :
                return $this->getTable()->getGroupService()->countAllChildrenForGroup($group, false);
        }
        
        return parent::render_cell($column, $group);
    }

    /**
     * @return SubSubscribedPlatformGroupTable|Table
     */
    protected function getTable()
    {
        return $this->get_table();
    }
}
