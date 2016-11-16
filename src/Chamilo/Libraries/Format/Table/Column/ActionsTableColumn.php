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

    /**
     *
     * @param string $headerCssClasses
     * @param string $contentCssClasses
     */
    public function __construct($headerCssClasses = null, $contentCssClasses = null)
    {
        return parent::__construct('action_column', '', $headerCssClasses, $contentCssClasses);
    }
}
