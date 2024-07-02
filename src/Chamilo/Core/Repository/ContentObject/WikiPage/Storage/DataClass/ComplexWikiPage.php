<?php
namespace Chamilo\Core\Repository\ContentObject\WikiPage\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassExtensionInterface;
use Chamilo\Libraries\Storage\DataClass\Traits\DataClassExtensionTrait;
use Chamilo\Libraries\Storage\Parameters\DataClassParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Core\Repository\ContentObject\WikiPage\Storage\DataClass
 */
class ComplexWikiPage extends ComplexContentObjectItem implements DataClassExtensionInterface
{
    use DataClassExtensionTrait;

    public const CONTEXT = WikiPage::CONTEXT;

    public const PROPERTY_IS_HOMEPAGE = 'is_homepage';
    public const PROPERTY_IS_LOCKED = 'is_locked';

    public static function getAdditionalPropertyNames(): array
    {
        return [self::PROPERTY_IS_HOMEPAGE, self::PROPERTY_IS_LOCKED];
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_complex_wiki_page';
    }

    public function get_is_homepage()
    {
        return $this->getAdditionalProperty(self::PROPERTY_IS_HOMEPAGE);
    }

    public function get_is_locked()
    {
        return $this->getAdditionalProperty(self::PROPERTY_IS_LOCKED);
    }

    public function set_is_homepage($value)
    {
        $this->setAdditionalProperty(self::PROPERTY_IS_HOMEPAGE, $value);
    }

    public function set_is_locked($value)
    {
        $this->setAdditionalProperty(self::PROPERTY_IS_LOCKED, $value);
    }

    public function update(): bool
    {
        if ($this->get_is_homepage())
        {
            $conditions = [];
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
                ), new StaticConditionVariable($this->get_parent()), ComplexContentObjectItem::getStorageUnitName()
            );
            $conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_ID
                    ), new StaticConditionVariable($this->get_id()), ComplexContentObjectItem::getStorageUnitName()
                )
            );

            $parameters = new DataClassParameters(condition: new AndCondition($conditions));

            $children = DataManager::retrieve_complex_content_object_items(
                ComplexContentObjectItem::class, $parameters
            );

            foreach ($children as $child)
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
