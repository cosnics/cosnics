<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entity\Group;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Table\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class EntityTableCellRenderer
    extends \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Entity\EntityTableCellRenderer
{

    public function render_cell($column, $entity)
    {
        if ($column->get_name() == EntityTableColumnModel::PROPERTY_GROUP_MEMBERS)
        {
            return $this->getGroupMembers($entity);
        }

        return parent::render_cell($column, $entity);
    }

    /**
     *
     * @param array $group
     *
     * @return string
     */
    protected function getGroupMembers($group)
    {
        $entityId = $group[Entry::PROPERTY_ENTITY_ID];
        $users = $this->getTable()->getEntityService()->getUsersForEntity($entityId);

        if (count($users) == 0)
        {
            return null;
        }

        $html = array();
        $html[] = '<select style="width:180px">';

        foreach ($users as $user)
        {
            $html[] = '<option>' . $user->get_fullname() . '</option>';
        }

        $html[] = '</select>';

        return implode(PHP_EOL, $html);
    }
}