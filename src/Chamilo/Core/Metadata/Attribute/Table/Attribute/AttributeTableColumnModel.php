<?php
namespace Chamilo\Core\Metadata\Attribute\Table\Attribute;

use Chamilo\Core\Metadata\Attribute\Storage\DataClass\Attribute;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Table column model for the schema
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AttributeTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const COLUMN_PREFIX = 'prefix';
    const COLUMN_CONTROLLED_VOCABULARY = 'controlled_vocabulary';

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(
            new StaticTableColumn(
                self :: COLUMN_PREFIX, 
                Translation :: get(
                    (string) StringUtilities :: getInstance()->createString(self :: COLUMN_PREFIX)->upperCamelize(), 
                    null, 
                    'core\metadata')));
        
        $this->add_column(
            new DataClassPropertyTableColumn(
                Attribute :: class_name(), 
                Attribute :: PROPERTY_NAME, 
                Translation :: get(
                    (string) StringUtilities :: getInstance()->createString(Attribute :: PROPERTY_NAME)->upperCamelize(), 
                    null, 
                    'core\metadata'), 
                false));
        
        $this->add_column(
            new DataClassPropertyTableColumn(
                Attribute :: class_name(), 
                Attribute :: PROPERTY_DISPLAY_NAME, 
                Translation :: get(
                    (string) StringUtilities :: getInstance()->createString(Attribute :: PROPERTY_DISPLAY_NAME)->upperCamelize(), 
                    null, 
                    'core\metadata'), 
                false));
        
        $this->add_column(
            new StaticTableColumn(
                self :: COLUMN_CONTROLLED_VOCABULARY, 
                Theme :: getInstance()->getImage(
                    'action/controlled_vocabulary', 
                    'png', 
                    Translation :: get('HasControlledVocabulary', null, 'core\metadata'), 
                    null, 
                    ToolbarItem :: DISPLAY_ICON, 
                    false, 
                    __NAMESPACE__)));
    }
}