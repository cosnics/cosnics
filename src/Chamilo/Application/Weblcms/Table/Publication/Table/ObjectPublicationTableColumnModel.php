<?php
namespace Chamilo\Application\Weblcms\Table\Publication\Table;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Column\ActionsTableColumn;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;

/**
 * The TableColumnModel for the object publication table
 *
 * @package application.weblcms
 * @author Original Author Unknown
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring to record table
 */
class ObjectPublicationTableColumnModel extends RecordTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const DEFAULT_ORDER_COLUMN_INDEX = 7;
    const COLUMN_STATUS = 'status';
    const COLUMN_PUBLISHED_FOR = 'published_for';

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
    public function initializeColumns($addActionsColumn = true)
    {
        $this->addColumn(new StaticTableColumn(self::COLUMN_STATUS, '', null, 'publication_table_status_column'));

        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TITLE));

        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION));

        $this->addColumn(
            new DataClassPropertyTableColumn(
                ContentObjectPublication::class,
                ContentObjectPublication::PROPERTY_PUBLICATION_DATE));

        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_MODIFICATION_DATE));

        $this->addColumn(
            new DataClassPropertyTableColumn(
                ContentObjectPublication::class,
                ContentObjectPublication::PROPERTY_PUBLISHER_ID));

        $this->addColumn(
            new StaticTableColumn(
                self::COLUMN_PUBLISHED_FOR,
                Translation::getInstance()->getTranslation('PublishedFor', null, Manager::context())));

        $this->addColumn(
            new DataClassPropertyTableColumn(
                ContentObjectPublication::class,
                ContentObjectPublication::PROPERTY_DISPLAY_ORDER_INDEX,
                null,
                true,
                null,
                'publication_table_order_column'));

        if ($addActionsColumn)
        {
            $this->addActionsColumn();
        }
    }

    /**
     * Adds the actions column
     */
    protected function addActionsColumn()
    {
        $this->addColumn(new ActionsTableColumn('publication_table_actions_column'));
    }

    /**
     * **************************************************************************************************************
     * Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the display order column property
     *
     * @return string
     */
    public function get_display_order_column_property()
    {
        return ContentObjectPublication::PROPERTY_DISPLAY_ORDER_INDEX;
    }

    /**
     * Checks whether or not a given column is a display order / sort column
     *
     * @return bool
     */
    public function is_display_order_column()
    {
        $orderedColumn = $this->getCurrentOrderedColumn();

        $firstOrderedColumn = $orderedColumn->getTableColumn();
        $firstOrderedColumnOrder = $orderedColumn->getOrderDirection();

        $display_order_column_property = $this->get_display_order_column_property();

        if ($firstOrderedColumn && $display_order_column_property && $firstOrderedColumnOrder == SORT_ASC)
        {
            $current_column_property = $firstOrderedColumn->get_name();

            if ($current_column_property == $display_order_column_property)
            {
                return true;
            }
        }

        return false;
    }
}
