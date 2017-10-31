<?php
namespace Chamilo\Libraries\Format\DataTable\Column;

use Chamilo\Libraries\Format\DataTable\Column\DataTableColumn;

/**
 *
 * @package Chamilo\Libraries\Format\DataTable\Column
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 */
class ActionsDataTableColumn extends DataTableColumn
{

    public function __construct()
    {
        return parent::__construct('actionColumn', '');
    }
}
