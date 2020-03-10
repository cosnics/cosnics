<?php
namespace Chamilo\Core\Repository\Table\ExternalLink;

use Chamilo\Core\Repository\External\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
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
                Instance::class_name(),
                Instance::PROPERTY_IMPLEMENTATION,
                Theme::getInstance()->getImage(
                    'Logo/' . Theme::ICON_MINI, 
                    'png', 
                    Translation::get('RepositoryType'), 
                    null, 
                    ToolbarItem::DISPLAY_ICON, 
                    false, 
                    Manager::context())));
        $this->add_column(
            new DataClassPropertyTableColumn(
                Instance::class_name(),
                Instance::PROPERTY_TITLE));
    }
}
