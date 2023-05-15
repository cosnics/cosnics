<?php
namespace Chamilo\Libraries\Architecture\Application;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Package\Action\Remover;
use Chamilo\Core\Menu\Storage\DataClass\ApplicationItem;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Base class for specific removal extensions of web applications
 *
 * @package Chamilo\Libraries\Architecture\Application
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class WebApplicationRemover extends Remover
{

    /**
     * @return bool
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     */
    public function extra()
    {
        $context = $this->context();

        if (!Configuration::getInstance()->isRegisteredAndActive('Chamilo\Core\Menu'))
        {
            return true;
        }

        $condition = new EqualityCondition(
            new PropertyConditionVariable(ApplicationItem::class, ApplicationItem::PROPERTY_APPLICATION),
            new StaticConditionVariable(ClassnameUtilities::getInstance()->getPackageNameFromNamespace($context))
        );

        $menu_item = DataManager::retrieve(
            ApplicationItem::class, new DataClassRetrieveParameters($condition)
        );

        if ($menu_item instanceof Item)
        {
            return $menu_item->delete();
        }

        return true;
    }
}
