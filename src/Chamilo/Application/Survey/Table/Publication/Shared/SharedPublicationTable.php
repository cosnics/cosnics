<?php
namespace Chamilo\Application\Survey\Table\Publication\Shared;

use Chamilo\Application\Survey\Table\Publication\PublicationTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;

/**
 *
 * @package Chamilo\Application\Survey\Table\Publication\Shared
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SharedPublicationTable extends PublicationTable
{

    /**
     *
     * @see \Chamilo\Application\Survey\Table\Publication\PublicationTable::get_implemented_form_actions()
     */
    public function get_implemented_form_actions()
    {
        return new TableFormActions(__NAMESPACE__, self :: TABLE_IDENTIFIER);
    }
}
