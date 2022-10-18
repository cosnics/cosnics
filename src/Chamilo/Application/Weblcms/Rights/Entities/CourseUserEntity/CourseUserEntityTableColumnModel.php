<?php
namespace Chamilo\Application\Weblcms\Rights\Entities\CourseUserEntity;

use Chamilo\Core\Rights\Editor\Table\LocationEntity\LocationEntityTableColumnModel;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;

class CourseUserEntityTableColumnModel extends LocationEntityTableColumnModel
{

    public function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_USERNAME));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_OFFICIAL_CODE));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_FIRSTNAME));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_LASTNAME));
        parent::initializeColumns();
    }
}
