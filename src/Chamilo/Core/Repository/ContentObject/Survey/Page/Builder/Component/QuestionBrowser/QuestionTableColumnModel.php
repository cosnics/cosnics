<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component\QuestionBrowser;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Platform\Translation;

class QuestionTableColumnModel extends DataClassTableColumnModel
{

    public function initialize_columns()
    {
        $columns = array();
        $columns[] = new DataClassPropertyTableColumn(
            ContentObject :: class_name(), 
            ContentObject :: PROPERTY_TITLE, 
            false);
        $columns[] = new DataClassPropertyTableColumn(
            ContentObject :: class_name(), 
            ContentObject :: PROPERTY_DESCRIPTION, 
            false);
        $columns[] = new DataClassPropertyTableColumn(
            ComplexContentObjectItem :: class_name(), 
            ComplexContentObjectItem :: PROPERTY_DISPLAY_ORDER, 
            true);
        $columns[] = new StaticTableColumn(Translation :: get('visible'));
        return $columns;
    }
}
?>