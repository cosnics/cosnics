<?php
namespace Chamilo\Core\Repository\Implementation\Office365Video\Table\ExternalObject;

use Chamilo\Core\Repository\External\Table\ExternalObject\DefaultExternalObjectTableColumnModel;
use Chamilo\Core\Repository\Implementation\Office365Video\ExternalObject;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;

class ExternalObjectTableColumnModel extends DefaultExternalObjectTableColumnModel
{

    public function initialize_columns()
    {
        $this->add_column(
            new DataClassPropertyTableColumn(
                ExternalObject :: class_name(),
                ExternalObject :: PROPERTY_TYPE,
                null,
                false));
        $this->add_column(
            new DataClassPropertyTableColumn(
                ExternalObject :: class_name(),
                ExternalObject :: PROPERTY_TITLE,
                null,
                true));
        $this->add_column(
            new DataClassPropertyTableColumn(
                ExternalObject :: class_name(),
                ExternalObject :: PROPERTY_DESCRIPTION,
                null,
                true));
        $this->add_column(
            new DataClassPropertyTableColumn(
                ExternalObject :: class_name(),
                ExternalObject :: PROPERTY_CREATED,
                null,
                true));
    }
}
