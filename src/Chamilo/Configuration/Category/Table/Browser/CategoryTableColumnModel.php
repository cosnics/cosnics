<?php
namespace Chamilo\Configuration\Category\Table\Browser;

use Chamilo\Configuration\Category\Storage\DataClass\PlatformCategory;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: category_browser_table_column_model.class.php 191 2009-11-13 11:50:28Z chellee $
 * 
 * @package application.common.category_manager.component.category_browser
 */

/**
 * Table column model for the user browser table
 */
class CategoryTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const CATEGORY = 'Categorie';
    const SUBCATEGORIES = 'Subcategories';

    public function initialize_columns()
    {
        $category_class_name = get_class($this->get_component()->get_parent()->get_category());

        $this->add_column(new StaticTableColumn(Translation::get(self::CATEGORY)));
        $this->add_column(
            new DataClassPropertyTableColumn($category_class_name, PlatformCategory::PROPERTY_NAME));
        if ($this->get_component()->get_subcategories_allowed())
        {
            $this->add_column(new StaticTableColumn(Translation::get(self::SUBCATEGORIES)));
        }
    }
}
