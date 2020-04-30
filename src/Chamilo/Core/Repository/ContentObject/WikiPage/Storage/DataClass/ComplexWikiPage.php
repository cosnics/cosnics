<?php
namespace Chamilo\Core\Repository\ContentObject\WikiPage\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package repository.lib.content_object.wiki_page
 */
class ComplexWikiPage extends ComplexContentObjectItem
{
    const PROPERTY_IS_HOMEPAGE = 'is_homepage';
    const PROPERTY_IS_LOCKED = 'is_locked';

    public static function get_additional_property_names()
    {
        return array(self::PROPERTY_IS_HOMEPAGE, self::PROPERTY_IS_LOCKED);
    }

    public function get_is_homepage()
    {
        return $this->get_additional_property(self::PROPERTY_IS_HOMEPAGE);
    }

    public function get_is_locked()
    {
        return $this->get_additional_property(self::PROPERTY_IS_LOCKED);
    }

    public function set_is_homepage($value)
    {
        $this->set_additional_property(self::PROPERTY_IS_HOMEPAGE, $value);
    }

    public function set_is_locked($value)
    {
        $this->set_additional_property(self::PROPERTY_IS_LOCKED, $value);
    }

    public function update()
    {
        if ($this->get_is_homepage())
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem::class,
                    ComplexContentObjectItem::PROPERTY_PARENT),
                new StaticConditionVariable($this->get_parent()),
                ComplexContentObjectItem::get_table_name());
            $conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        ComplexContentObjectItem::class,
                        ComplexContentObjectItem::PROPERTY_ID),
                    new StaticConditionVariable($this->get_id()),
                    ComplexContentObjectItem::get_table_name()));

            $parameters = new DataClassRetrievesParameters(new AndCondition($conditions));

            $children = DataManager::retrieve_complex_content_object_items(
                ComplexContentObjectItem::class,
                $parameters);

            while ($child = $children->next_result())
            {
                if ($child->get_is_homepage())
                {
                    $child->set_is_homepage(0);
                    $child->update();
                    break;
                }
            }
        }

        return parent::update();
    }
}
