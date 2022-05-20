<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\WikiPage\Storage\DataClass\WikiPage;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package repository.lib.content_object.wiki
 */
class Wiki extends ContentObject implements ComplexContentObjectSupport
{
    const PROPERTY_LOCKED = 'locked';
    const PROPERTY_LINKS = 'links';

    public static function getTypeName(): string
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class, true);
    }

    public function get_allowed_types()
    {
        return array(WikiPage::class);
    }

    public function get_locked()
    {
        return $this->getAdditionalProperty(self::PROPERTY_LOCKED);
    }

    public function set_locked($locked)
    {
        return $this->setAdditionalProperty(self::PROPERTY_LOCKED, $locked);
    }

    public function get_links()
    {
        return $this->getAdditionalProperty(self::PROPERTY_LINKS);
    }

    public function set_links($links)
    {
        return $this->setAdditionalProperty(self::PROPERTY_LINKS, $links);
    }

    public static function getAdditionalPropertyNames(): array
    {
        return array(self::PROPERTY_LOCKED, self::PROPERTY_LINKS);
    }

    public function get_wiki_pages($return_complex_items = false)
    {
        $complex_content_objects = DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class,
            new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem::class,
                    ComplexContentObjectItem::PROPERTY_PARENT),
                new StaticConditionVariable($this->get_id()),
                ComplexContentObjectItem::getTableName()));

        if ($return_complex_items)
        {
            return $complex_content_objects;
        }

        $wiki_pages = [];

        foreach($complex_content_objects as $complex_content_object)
        {
            $wiki_pages[] = DataManager::retrieve_by_id(
                ContentObject::class,
                $complex_content_object->get_ref());
        }

        return $wiki_pages;
    }

    public function get_wiki_pages_by_title(Condition $title_condition)
    {
        $complex_content_object_item_condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class,
                ComplexContentObjectItem::PROPERTY_PARENT),
            new StaticConditionVariable($this->get_id()));

        $content_object_conditions = [];
        $content_object_conditions[] = $title_condition;
        $content_object_conditions[] = new SubselectCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID),
            new PropertyConditionVariable(
                ComplexContentObjectItem::class,
                ComplexContentObjectItem::PROPERTY_REF),
            $complex_content_object_item_condition);
        $content_object_condition = new AndCondition($content_object_conditions);

        return DataManager::retrieve_active_content_objects(
            ContentObject::class,
            $content_object_condition);
    }

    /**
     * Returns the names of the properties which are UI-wise filled by the integrated html editor
     *
     * @return string[]
     */
    public static function get_html_editors($html_editors = [])
    {
        return parent::get_html_editors(array(self::PROPERTY_LINKS));
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'repository_wiki';
    }
}
