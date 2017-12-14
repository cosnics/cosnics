<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entry\CourseGroup;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;

/**
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entry\CourseGroup
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntryTableColumnModel extends \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entry\Group\EntryTableColumnModel
{
    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(CourseGroup::class_name(), CourseGroup::PROPERTY_NAME));

        parent::initialize_columns();
    }
}