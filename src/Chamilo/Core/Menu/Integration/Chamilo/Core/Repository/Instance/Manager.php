<?php
namespace Chamilo\Core\Menu\Integration\Chamilo\Core\Repository\Instance;

use Chamilo\Core\Menu\ItemTitles;
use Chamilo\Core\Menu\Storage\DataClass\CategoryItem;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Core\Menu\Storage\DataClass\LinkApplicationItem;
use Chamilo\Core\Menu\Storage\DataClass\LinkItem;
use Chamilo\Core\Menu\Storage\DataManager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Cache\DataClassResultCache;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Core\Menu\Integration\Chamilo\Core\Repository\Instance
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Manager
{

    static public function process_instance(Instance $instance)
    {
        $category_name = Translation :: get('ExternalRepositories');

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ItemTitle :: class_name(), ItemTitle :: PROPERTY_TITLE),
            new StaticConditionVariable($category_name));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ItemTitle :: class_name(), ItemTitle :: PROPERTY_ISOCODE),
            new StaticConditionVariable(Translation :: getInstance()->getLanguageIsocode()));

        $parameters = new DataClassDistinctParameters(new AndCondition($conditions), ItemTitle :: PROPERTY_ITEM_ID);
        DataClassResultCache :: truncate(ItemTitle :: class_name());
        $titles = DataManager :: distinct(ItemTitle :: class_name(), $parameters);

        if (count($titles) > 0)
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Item :: class_name(), Item :: PROPERTY_TYPE),
                new StaticConditionVariable(CategoryItem :: class_name()));
            $conditions[] = new InCondition(
                new PropertyConditionVariable(Item :: class_name(), Item :: PROPERTY_ID),
                $titles);

            $category = DataManager :: retrieve(CategoryItem :: class_name(), new AndCondition($conditions));
        }
        else
        {
            $languages = \Chamilo\Configuration\Storage\DataManager :: get_languages();
            $category = new CategoryItem();
            $category->set_parent(0);

            $item_titles = new ItemTitles();
            foreach ($languages as $isocode => $language)
            {
                $item_title = new ItemTitle();
                $item_title->set_title(Translation :: get('ExternalRepositories', null, __NAMESPACE__, $isocode));
                $item_title->set_isocode($isocode);
                $item_titles->add($item_title);
            }

            $category->set_titles($item_titles);
            if (! $category->create())
            {
                return false;
            }
        }

        $item = new LinkApplicationItem();

        $item->set_section($instance->get_type());
        $item->set_target(LinkItem :: TARGET_SELF);
        $item->set_parent($category->get_id());

        $redirect = new Redirect(
            array(
                Application :: PARAM_CONTEXT => $instance->get_implementation(),
                \Chamilo\Core\Repository\External\Manager :: PARAM_EXTERNAL_REPOSITORY => $instance->get_id()));

        $item->set_url($redirect->getUrl());
        if (! $item->create())
        {
            return false;
        }
        else
        {
            $item_title = new ItemTitle();
            $item_title->set_title($instance->get_title());
            $item_title->set_isocode(Translation :: getInstance()->getLanguageIsocode());
            $item_title->set_item_id($item->get_id());
            if (! $item_title->create())
            {
                return false;
            }
        }
        return true;
    }
}

