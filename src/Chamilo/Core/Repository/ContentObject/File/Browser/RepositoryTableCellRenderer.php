<?php
namespace Chamilo\Core\Repository\ContentObject\File\Browser;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Format\Table\Column\TableColumn;

/**
 * Cell rendere for the object browser table
 */
class RepositoryTableCellRenderer extends \Chamilo\Core\Repository\Table\ContentObject\Table\RepositoryTableCellRenderer
{
    // Inherited
    public function renderCell(TableColumn $column, $content_object): string
    {
        switch ($column->get_name())
        {
            case File::PROPERTY_FILESIZE :
                return Filesystem::format_file_size($content_object->get_filesize());
        }
        
        return parent::renderCell($column, $content_object);
    }
}
