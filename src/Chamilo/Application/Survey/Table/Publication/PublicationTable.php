<?php
namespace Chamilo\Application\Survey\Table\Publication;

use Chamilo\Application\Survey\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Application\Survey\Table\Publication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PublicationTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager :: PARAM_PUBLICATION_ID;

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport::get_implemented_form_actions()
     */
    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__, self :: TABLE_IDENTIFIER);

        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(
                    array(
                        Manager :: PARAM_ACTION => Manager :: ACTION_FAVOURITE,
                        \Chamilo\Application\Survey\Favourite\Manager :: PARAM_ACTION => \Chamilo\Application\Survey\Favourite\Manager :: ACTION_CREATE)),
                Translation :: get('FavouriteSelected', null, Utilities :: COMMON_LIBRARIES),
                true));

        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(array(Manager :: PARAM_ACTION => Manager :: ACTION_DELETE)),
                Translation :: get('DeleteSelected', null, Utilities :: COMMON_LIBRARIES),
                true));

        return $actions;
    }
}
