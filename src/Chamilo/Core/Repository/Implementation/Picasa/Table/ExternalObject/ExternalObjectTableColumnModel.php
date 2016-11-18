<?php
namespace Chamilo\Core\Repository\Implementation\Picasa\Table\ExternalObject;

use Chamilo\Core\Repository\External\Table\ExternalObject\DefaultExternalObjectTableColumnModel;
use Chamilo\Core\Repository\Implementation\Picasa\ExternalObject;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;

class ExternalObjectTableColumnModel extends DefaultExternalObjectTableColumnModel
{

    public function initialize_columns()
    {
        parent::initialize_columns();
        $this->add_column(
            new DataClassPropertyTableColumn(ExternalObject::class_name(), ExternalObject::PROPERTY_LICENSE));
    }
}
