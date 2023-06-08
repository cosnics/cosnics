<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Home\Package;

use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Home\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{
    public const CONTEXT = \Chamilo\Core\User\Integration\Chamilo\Core\Home\Manager::CONTEXT;

    public function extra()
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                Element::class, Element::PROPERTY_TITLE
            ), new StaticConditionVariable(Translation::get('Various', null, 'Chamilo\Core\Home'))
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                Element::class, Element::PROPERTY_USER_ID
            ), new StaticConditionVariable(0)
        );

        $condition = new AndCondition($conditions);

        $parameters = new DataClassRetrieveParameters($condition);
        $column = DataManager::retrieve(
            Element::class, $parameters
        );

        if ($column instanceof Column)
        {

            $block = new Block();

            $block->setParentId($column->getId());
            $block->setContext(Manager::CONTEXT);
            $block->setBlockType('Login');
            $block->setVisibility(true);
            $block->setTitle(Translation::get('User', null, Manager::CONTEXT));
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
