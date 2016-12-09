<?php
namespace Chamilo\Core\Repository\ContentObject\Webpage\Browser;

use Chamilo\Core\Repository\ContentObject\Webpage\Storage\DataClass\Webpage;
use Chamilo\Libraries\File\Filesystem;

/**
 * Cell rendere for the object browser table
 */
class RepositoryTableCellRenderer extends \Chamilo\Core\Repository\Table\ContentObject\Table\RepositoryTableCellRenderer
{
    // Inherited
    public function render_cell($column, $content_object)
    {
        switch ($column->get_name())
        {
            case Webpage::PROPERTY_FILESIZE :
                return Filesystem::format_file_size($content_object->get_filesize());
        }
        
        return parent::render_cell($column, $content_object);
    }
}
