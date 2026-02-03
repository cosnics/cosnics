<?php
namespace Chamilo\Libraries\UserInterface\Table\Architecture\Interface;

use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\TableResultPosition;

/**
 * @package Chamilo\Libraries\UserInterface\Table\Architecture\Interface
 * @author  Sven Vanpoucke
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface TableRowActionsSupport
{
    /**
     * @param \Chamilo\Libraries\Storage\Architecture\Domain\DataClass|string[] $result
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, mixed $result): string;
}
