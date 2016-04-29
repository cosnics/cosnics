<?php
namespace Chamilo\Core\Admin\Integration\Chamilo\Core\Home\Package;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class Installer extends \Chamilo\Configuration\Package\Action\Installer
{

    public function extra()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Core\Home\Storage\DataClass\Element:: class_name(),
                \Chamilo\Core\Home\Storage\DataClass\Element :: PROPERTY_TITLE
            ),
            new StaticConditionVariable(Translation:: get('News', null, 'Chamilo\Core\Home'))
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Core\Home\Storage\DataClass\Element:: class_name(),
                \Chamilo\Core\Home\Storage\DataClass\Element :: PROPERTY_USER_ID
            ),
            new StaticConditionVariable(0)
        );

        $condition = new AndCondition($conditions);

        $parameters = new DataClassRetrieveParameters($condition);
        $column = \Chamilo\Core\Home\Storage\DataManager:: retrieve(
            \Chamilo\Core\Home\Storage\DataClass\Element:: class_name(),
            $parameters
        );

        if ($column instanceof \Chamilo\Core\Home\Storage\DataClass\Column)
        {

            $block = new \Chamilo\Core\Home\Storage\DataClass\Block();

            $block->setParentId($column->getId());
            $block->setContext(\Chamilo\Core\Admin\Manager:: context());
            $block->setBlockType('PortalHome');
            $block->setVisibility(true);
            $block->setTitle(Translation:: get('PortalHome', null, \Chamilo\Core\Admin\Manager:: context()));
            $block->setUserId(0);

            if (!$block->create())
            {
                return false;
            }

            return true;
        }
        else
        {
            return false;
        }
    }
}
