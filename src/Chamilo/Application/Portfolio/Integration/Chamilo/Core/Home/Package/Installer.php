<?php
namespace Chamilo\Application\Portfolio\Integration\Chamilo\Core\Home\Package;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Application\Portfolio\Integration\Chamilo\Core\Home\Package
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
                \Chamilo\Core\Home\Storage\DataClass\Element::class_name(), 
                \Chamilo\Core\Home\Storage\DataClass\Element::PROPERTY_TITLE), 
            new StaticConditionVariable(Translation::get('Various', null, 'Chamilo\Core\Home')));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Core\Home\Storage\DataClass\Element::class_name(), 
                \Chamilo\Core\Home\Storage\DataClass\Element::PROPERTY_USER_ID), 
            new StaticConditionVariable(0));
        
        $condition = new AndCondition($conditions);
        
        $parameters = new DataClassRetrieveParameters($condition);
        $column = \Chamilo\Core\Home\Storage\DataManager::retrieve(
            \Chamilo\Core\Home\Storage\DataClass\Element::class_name(), 
            $parameters);
        
        if ($column instanceof \Chamilo\Core\Home\Storage\DataClass\Column)
        {
            
            $block = new \Chamilo\Core\Home\Storage\DataClass\Block();
            
            $block->setParentId($column->getId());
            $block->setContext(\Chamilo\Application\Portfolio\Manager::context());
            $block->setBlockType('FavouriteUsers');
            $block->setVisibility(true);
            $block->setTitle(Translation::get('User', null, \Chamilo\Application\Portfolio\Manager::context()));
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
