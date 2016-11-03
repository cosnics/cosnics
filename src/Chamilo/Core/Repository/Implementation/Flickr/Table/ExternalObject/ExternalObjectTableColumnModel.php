<?php
namespace Chamilo\Core\Repository\Implementation\Flickr\Table\ExternalObject;

use Chamilo\Core\Repository\External\Table\ExternalObject\DefaultExternalObjectTableColumnModel;
use Chamilo\Core\Repository\Implementation\Flickr\ExternalObject;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;

class ExternalObjectTableColumnModel extends DefaultExternalObjectTableColumnModel
{

    public function initialize_columns()
    {
        $this->add_column(
            new DataClassPropertyTableColumn(ExternalObject :: class_name(), ExternalObject :: PROPERTY_TYPE, false));
        $this->add_column(
            new DataClassPropertyTableColumn(ExternalObject :: class_name(), ExternalObject :: PROPERTY_TITLE, false));
        $this->add_column(
            new DataClassPropertyTableColumn(
                ExternalObject :: class_name(), 
                ExternalObject :: PROPERTY_DESCRIPTION, 
                false));
        $this->add_column(
            new DataClassPropertyTableColumn(ExternalObject :: class_name(), ExternalObject :: PROPERTY_CREATED));
        $this->add_column(
            new DataClassPropertyTableColumn(ExternalObject :: class_name(), ExternalObject :: PROPERTY_LICENSE, false));
    }
}
