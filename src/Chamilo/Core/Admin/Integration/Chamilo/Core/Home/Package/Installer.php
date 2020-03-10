<?php
namespace Chamilo\Core\Admin\Integration\Chamilo\Core\Home\Package;

use Chamilo\Core\Admin\Manager;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataClass\Column;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Translation\Translation;
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
                Element::class_name(),
                Element::PROPERTY_TITLE),
            new StaticConditionVariable(Translation::get('News', null, 'Chamilo\Core\Home')));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                Element::class_name(),
                Element::PROPERTY_USER_ID),
            new StaticConditionVariable(0));
        
        $condition = new AndCondition($conditions);
        
        $parameters = new DataClassRetrieveParameters($condition);
        $column = DataManager::retrieve(
            Element::class_name(),
            $parameters);
        
        if ($column instanceof Column)
        {
            
            $block = new Block();
            
            $block->setParentId($column->getId());
            $block->setContext(Manager::context());
            $block->setBlockType('PortalHome');
            $block->setVisibility(true);
            $block->setTitle(Translation::get('PortalHome', null, Manager::context()));
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
