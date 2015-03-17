<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Alternative\Table\ContentObjectAlternative;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Storage\DataClass\ContentObjectMetadataElementValue;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 * Table column model for the ContentObjectAlternative data class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectAlternativeTableColumnModel extends RecordTableColumnModel implements
    TableColumnModelActionsColumnSupport
{
    const DEFAULT_ORDER_COLUMN_INDEX = 1;

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(
            new StaticTableColumn(
                ContentObject :: PROPERTY_TYPE,
                Theme :: getInstance()->getCommonImage(
                    'Action/Category',
                    'png',
                    Translation :: get('Type', \Chamilo\Core\Repository\Manager :: context()),
                    null,
                    ToolbarItem :: DISPLAY_ICON)));

        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject :: class_name(), ContentObject :: PROPERTY_TITLE, null));

        $this->add_column(
            new DataClassPropertyTableColumn(ContentObject :: class_name(), ContentObject :: PROPERTY_DESCRIPTION, null));

        $this->add_column(
            new DataClassPropertyTableColumn(
                \Chamilo\Core\Metadata\Element\Storage\DataClass\Element :: class_name(),
                \Chamilo\Core\Metadata\Element\Storage\DataClass\Element :: PROPERTY_DISPLAY_NAME,
                Translation :: get('MetadataElement')));

        $this->add_column(
            new StaticTableColumn(
                ContentObjectMetadataElementValue :: PROPERTY_VALUE,
                Translation :: get('MetadataElementValue')));
    }
}