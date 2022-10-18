<?php
namespace Chamilo\Core\Repository\Implementation\Flickr\Table\ExternalObject;

use Chamilo\Core\Repository\External\Table\ExternalObject\DefaultExternalObjectTableColumnModel;
use Chamilo\Core\Repository\Implementation\Flickr\ExternalObject;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;

class ExternalObjectTableColumnModel extends DefaultExternalObjectTableColumnModel
{

    public function initializeColumns()
    {
        $this->addColumn(
            new DataClassPropertyTableColumn(ExternalObject::class, ExternalObject::PROPERTY_TYPE, false));
        $this->addColumn(
            new DataClassPropertyTableColumn(ExternalObject::class, ExternalObject::PROPERTY_TITLE, false));
        $this->addColumn(
            new DataClassPropertyTableColumn(ExternalObject::class, ExternalObject::PROPERTY_DESCRIPTION, false));
        $this->addColumn(
            new DataClassPropertyTableColumn(ExternalObject::class, ExternalObject::PROPERTY_CREATED));
        $this->addColumn(
            new DataClassPropertyTableColumn(ExternalObject::class, ExternalObject::PROPERTY_LICENSE, false));
    }
}
