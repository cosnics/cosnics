<?php
namespace Chamilo\Libraries\Architecture\Application;

use Chamilo\Core\Menu\Storage\DataClass\ApplicationItem;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Base class for specific removal extensions of web applications
 * 
 * @author Hans De Bisschop
 */
class WebApplicationRemover extends \Chamilo\Configuration\Package\Action\Remover
{

    /**
     *
     * @return boolean
     */
    public function extra()
    {
        $context = $this->context();
        
        if (! \Chamilo\Configuration\Configuration::getInstance()->isRegisteredAndActive('Chamilo\Core\Menu'))
        {
            return true;
        }
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(ApplicationItem::class_name(), ApplicationItem::PROPERTY_APPLICATION), 
            new StaticConditionVariable(ClassnameUtilities::getInstance()->getPackageNameFromNamespace($context)));
        
        $menu_item = \Chamilo\Core\Menu\Storage\DataManager::retrieve(
            ApplicationItem::class_name(), 
            new DataClassRetrieveParameters($condition));
        
        if ($menu_item instanceof \Chamilo\Core\Menu\Storage\DataClass\Item)
        {
            return $menu_item->delete();
        }
        
        return true;
    }
}
