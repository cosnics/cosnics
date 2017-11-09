<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Table\WikiPage;

use Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package repository.lib.complex_display.wiki.component.wiki_page_table
 */
class WikiPageTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = \Chamilo\Core\Repository\Display\Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID;

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(
                    array(Manager::PARAM_ACTION => Manager::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM)),
                Translation::get('RemoveSelected', null, Utilities::COMMON_LIBRARIES)));
        return $actions;
    }
}