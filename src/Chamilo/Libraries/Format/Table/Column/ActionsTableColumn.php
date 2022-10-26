<?php
namespace Chamilo\Libraries\Format\Table\Column;

/**
 * You can add this column automatically to your table by implementing the
 * TableRowActionsSupport interface in your table.
 *
 * @package Chamilo\Libraries\Format\Table\Column
 * @author  Sven Vanpoucke
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActionsTableColumn extends StaticTableColumn
{
    /**
     * @param string[] $headerCssClasses
     * @param string[] $contentCssClasses
     */
    public function __construct(?array $headerCssClasses = null, ?array $contentCssClasses = null)
    {
        return parent::__construct('action_column', '', $headerCssClasses, $contentCssClasses);
    }
}
