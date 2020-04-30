<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Home\Package;

use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataClass\Column;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Core\User\Integration\Chamilo\Core\Home\Package
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{

    public function extra()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                Element::class,
                Element::PROPERTY_TITLE),
            new StaticConditionVariable(Translation::get('Various', null, 'Chamilo\Core\Home')));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                Element::class,
                Element::PROPERTY_USER_ID),
            new StaticConditionVariable(0));
        
        $condition = new AndCondition($conditions);
        
        $parameters = new DataClassRetrieveParameters($condition);
        $column = DataManager::retrieve(
            Element::class,
            $parameters);
        
        if ($column instanceof Column)
        {
            
            $block = new Block();
            
            $block->setParentId($column->getId());
            $block->setContext(Manager::context());
            $block->setBlockType('Login');
            $block->setVisibility(true);
            $block->setTitle(Translation::get('User', null, Manager::context()));
            $block->setUserId(0);
            
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
}
