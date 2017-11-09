<?php
namespace Chamilo\Core\Repository\Table\ExternalLink;

use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;

class ExternalLinkTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    public function initialize_columns()
    {
        $this->add_column(
            new DataClassPropertyTableColumn(
                \Chamilo\Core\Repository\Instance\Storage\DataClass\Instance::class_name(), 
                \Chamilo\Core\Repository\Instance\Storage\DataClass\Instance::PROPERTY_IMPLEMENTATION, 
                Theme::getInstance()->getImage(
                    'Logo/' . Theme::ICON_MINI, 
                    'png', 
                    Translation::get('RepositoryType'), 
                    null, 
                    ToolbarItem::DISPLAY_ICON, 
                    false, 
                    \Chamilo\Core\Repository\External\Manager::context())));
        $this->add_column(
            new DataClassPropertyTableColumn(
                \Chamilo\Core\Repository\Instance\Storage\DataClass\Instance::class_name(), 
                \Chamilo\Core\Repository\Instance\Storage\DataClass\Instance::PROPERTY_TITLE));
    }
}
