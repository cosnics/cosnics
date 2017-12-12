<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entity\PlatformGroup;

use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;

/**
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entity\PlatformGroup
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityTableColumnModel
    extends \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entity\Group\EntityTableColumnModel
{
    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Group::class_name(), Group::PROPERTY_NAME));

        parent::initialize_columns();

    }
}