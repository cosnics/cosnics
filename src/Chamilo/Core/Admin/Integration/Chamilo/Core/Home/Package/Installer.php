<?php
namespace Chamilo\Core\Admin\Integration\Chamilo\Core\Home\Package;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class Installer extends \Chamilo\Core\Home\Action\Installer
{

    public function extra()
    {
        if (parent :: extra())
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Core\Home\Storage\DataClass\Column :: class_name(),
                    \Chamilo\Core\Home\Storage\DataClass\Column :: PROPERTY_TITLE),
                new StaticConditionVariable(Translation :: get('News', null, 'Chamilo\Core\Home')));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Core\Home\Storage\DataClass\Column :: class_name(),
                    \Chamilo\Core\Home\Storage\DataClass\Column :: PROPERTY_USER),
                new StaticConditionVariable(0));
            $condition = new AndCondition($conditions);

            $parameters = new DataClassRetrieveParameters($condition);
            $column = \Chamilo\Core\Home\Storage\DataManager :: retrieve(
                \Chamilo\Core\Home\Storage\DataClass\Column :: class_name(),
                $parameters);

            if ($column instanceof \Chamilo\Core\Home\Storage\DataClass\Column)
            {

                $block = new \Chamilo\Core\Home\Storage\DataClass\Block();
                $block->set_column($column->get_id());
                $block->set_title(Translation :: get('PortalHome', null, \Chamilo\Core\Admin\Manager :: context()));
                $registration = \Chamilo\Core\Home\Storage\DataManager :: retrieve_home_block_registration_by_context_and_block(
                    static :: package(),
                    'PortalHome');
                $block->set_registration_id($registration->get_id());
                $block->set_user('0');
                if (! $block->create())
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
        else
        {
            return false;
        }
    }
}
