<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Publication;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Table\Publication\Table\ObjectPublicationTableColumnModel;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Translation\Translation;

/**
 * Extension on the content object publication table column model for this tool
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublicationTableColumnModel extends ObjectPublicationTableColumnModel
{
    const DEFAULT_ORDER_COLUMN_INDEX = 8;

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Initializes the columns for the table
     *
     * @param bool $addActionsColumn
     */
    public function initialize_columns($addActionsColumn = false)
    {
        parent::initialize_columns($addActionsColumn);

        $this->add_column(
            new DataClassPropertyTableColumn(Assignment::class_name(), Assignment::PROPERTY_END_TIME, null, false)
        );

        $this->add_column(new StaticTableColumn(Publication::PROPERTY_ENTITY_TYPE, ''), 1);

        $this->addActionsColumn();

        if (!$this->get_component()->get_tool_browser()->get_parent()->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $this->delete_column(7);
            $this->delete_column(7);
        }
        else
        {
            $this->add_column(
                new StaticTableColumn(
                    Manager::PROPERTY_NUMBER_OF_SUBMISSIONS,
                    Translation::getInstance()->getTranslation('NumberOfSubmissions', null, Manager::context())
                )
            );
        }
    }
}
