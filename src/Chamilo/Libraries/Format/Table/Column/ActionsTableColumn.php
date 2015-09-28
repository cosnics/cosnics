<?php
namespace Chamilo\Libraries\Format\Table\Column;

/**
 * This class describes an action column.
 * You can add this column automatically to your table by implementing the
 * TableColumnModelActionsColumnSupport interface in your column model.
 * 
 * @package \libraries;
 * @author Sven Vanpoucke
 */
class ActionsTableColumn extends StaticTableColumn
{

    public function __construct()
    {
        return parent :: __construct('action_column', '');
    }
}
