<?php
namespace Chamilo\Libraries\Format\Table\Interface;

use Chamilo\Libraries\Format\Table\TableResultPosition;

/**
 * @package Chamilo\Libraries\Format\Table\Interfaces
 * @author  Sven Vanpoucke
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface TableRowActionsSupport
{

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|string[] $result
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $result): string;
}
