<?php
namespace Chamilo\Core\Repository\Ajax\Component;

use Chamilo\Application\Weblcms\Ajax\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;
use stdClass;

/**
 * @package Chamilo\Core\Repository\Ajax\Component
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AttachmentContentObjectsFeedComponent extends Manager
{
    const PARAM_EXCLUDE_CONTENT_OBJECT_IDS = 'exclude_content_object_ids';
    const PARAM_FILTER = 'filter';
    const PARAM_OFFSET = 'offset';
    const PARAM_SEARCH_QUERY = 'query';

    const PROPERTY_ELEMENTS = 'elements';
    const PROPERTY_TOTAL_ELEMENTS = 'total_elements';

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function run()
    {
        $result = new JsonAjaxResult();

        $result->set_property(self::PROPERTY_ELEMENTS, $this->getElements());
        $result->set_property(self::PROPERTY_TOTAL_ELEMENTS, $this->countContentObjects());

        $result->display();
    }

    /**
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement $myRepositoryElement
     */
    protected function addCategoryElement(AdvancedElementFinderElement $myRepositoryElement)
    {
        $glyph = new FontAwesomeGlyph('folder', array('fa-fw'), null, 'fas');

        /**
         * @var \Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory[] $category
         */
        $categories = $this->retrieveCategories();

        if (count($categories))
        {
            foreach ($categories as $category)
            {
                $myRepositoryElement->add_child(
                    new AdvancedElementFinderElement(
                        'category_' . $category->getId(), $glyph->getClassNamesString(), $category->get_name(),
                        $category->get_name(), AdvancedElementFinderElement::TYPE_FILTER
                    )
                );
            }
        }
    }

    /**
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement $myRepositoryElement
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function addContentObjectElement(AdvancedElementFinderElement $myRepositoryElement)
    {
        /**
         * @var \Chamilo\Core\Repository\Storage\DataClass\ContentObject[] $contentObject
         */
        $contentObjects = $this->retrieveContentObjects();

        if (count($contentObjects))
        {
            foreach ($contentObjects as $contentObject)
            {
                $myRepositoryElement->add_child(
                    new AdvancedElementFinderElement(
                        'content_object_' . $contentObject->getId(),
                        $contentObject->getGlyph(IdentGlyph::SIZE_MINI, true, array('fa-fw'))->getClassNamesString(),
                        $contentObject->get_title(), $contentObject->get_type_string()
                    )
                );
            }
        }
    }

    /**
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement $myRepositoryElement
     */
    protected function addSearchQueryContentObjectElement(AdvancedElementFinderElement $myRepositoryElement)
    {
        $contentObjects = $this->retrieveContentObjects();
        $contentObjectCategories = array();

        foreach ($contentObjects as $contentObject)
        {
            $parentContentObjectIdentifiers = array();

            if ($contentObject->get_parent_id() != 0)
            {
                $category =
                    DataManager::retrieve_by_id(RepositoryCategory::class_name(), $contentObject->get_parent_id());
                $parentContentObjectIdentifiers = $category->get_parent_ids();
            }

            $parentContentObjectIdentifiers[] = $contentObject->get_parent_id();

            $previousParent = null;

            foreach ($parentContentObjectIdentifiers as $parentContentObjectIdentifier)
            {
                if (!array_key_exists($parentContentObjectIdentifier, $contentObjectCategories))
                {
                    $contentObjectCategory = new stdClass();

                    $contentObjectCategory->categoryIdentifier = $parentContentObjectIdentifier;
                    $contentObjectCategory->categoryObject =
                        DataManager::retrieve_by_id(RepositoryCategory::class_name(), $parentContentObjectIdentifier);
                    $contentObjectCategory->subCategories = array();
                    $contentObjectCategory->contentObjects = array();

                    $contentObjectCategories[$parentContentObjectIdentifier] = $contentObjectCategory;
                }

                if (!is_null($previousParent))
                {
                    if (!array_key_exists(
                        $parentContentObjectIdentifier, $contentObjectCategories[$previousParent]->subCategories
                    ))
                    {
                        $contentObjectCategories[$previousParent]->subCategories[$parentContentObjectIdentifier] =
                            $contentObjectCategories[$parentContentObjectIdentifier];
                    }
                }

                $previousParent = $parentContentObjectIdentifier;
            }

            $contentObjectCategories[$contentObject->get_parent_id()]->contentObjects[$contentObject->getId()] =
                $contentObject;
        }

        var_dump($contentObjectCategories[0]);

        $this->addContentObjectElement($myRepositoryElement);
    }

    protected function countContentObjects()
    {
        return DataManager::count_active_content_objects(
            ContentObject::class_name(), new DataClassCountParameters($this->getContentObjectConditions())
        );
    }

    /**
     * @return integer[]
     */
    protected function getCategoryIdentifiers()
    {
        $searchQuery = $this->getSearchQuery();

        if (!empty($searchQuery))
        {
            if ($this->getFilter() == 0)
            {
                return array();
            }

            $category = DataManager::retrieve_by_id(RepositoryCategory::class_name(), $this->getFilter());

            if ($category instanceof RepositoryCategory)
            {
                return $category->get_children_ids();
            }
            else
            {
                return array();
            }
        }
        else
        {
            return array($this->getFilter());
        }
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function getContentObjectConditions()
    {
        $excludedContentObjectIdentifiers = $this->getPostDataValue(self::PARAM_EXCLUDE_CONTENT_OBJECT_IDS);
        $searchQuery = $this->getSearchQuery();

        $conditions = array();

        if (!empty($searchQuery))
        {
            $conditions[] = Utilities::query_to_condition(
                $searchQuery,
                array(new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_TITLE))
            );
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_OWNER_ID),
            new StaticConditionVariable($this->getUser()->getId())
        );

        $categoryIdentifiers = $this->getCategoryIdentifiers();

        if (count($categoryIdentifiers) > 0)
        {
            $conditions[] = new InCondition(
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_PARENT_ID),
                $categoryIdentifiers
            );
        }

        $conditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_STATE),
                new StaticConditionVariable(ContentObject::STATE_RECYCLED)
            )
        );

        if (is_array($excludedContentObjectIdentifiers) && count($excludedContentObjectIdentifiers) > 0)
        {
            $excludeConditions = array();
            foreach ($excludedContentObjectIdentifiers as $excludedContentObjectIdentifier)
            {
                $excludeConditions[] = new EqualityCondition(
                    new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_ID),
                    new StaticConditionVariable($excludedContentObjectIdentifier)
                );
            }
            $conditions[] = new NotCondition(new OrCondition($excludeConditions));
        }

        $conditions[] = new InCondition(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_TYPE),
            DataManager::get_registered_types()
        );

        return new AndCondition($conditions);
    }

    /**
     * @return \string[][]
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function getElements()
    {
        $elements = new AdvancedElementFinderElements();

        $glyph = new FontAwesomeGlyph('hdd', array('fa-fw'), null, 'fas');

        $myRepository = $this->getTranslator()->trans('MyRepository', array(), Utilities::COMMON_LIBRARIES);

        $myRepositoryElement = new AdvancedElementFinderElement(
            'my_repository', $glyph->getClassNamesString(), $myRepository, $myRepository,
            AdvancedElementFinderElement::TYPE_VISUAL
        );

        if ($this->getSearchQuery())
        {
            $this->addSearchQueryContentObjectElement($myRepositoryElement);
        }
        else
        {


            $this->addCategoryElement($myRepositoryElement);
            $this->addContentObjectElement($myRepositoryElement);
        }

        if ($myRepositoryElement->hasChildren())
        {
            $elements->add_element($myRepositoryElement);
        }

        return $elements->as_array();
    }

    /**
     * @return integer
     */
    protected function getFilter()
    {
        $filterValue = $this->getRequest()->request->get(self::PARAM_FILTER);

        if ($filterValue)
        {
            $filterValues = explode('_', $filterValue);

            if (count($filterValues) == 2 && $filterValues[0] == 'category')
            {
                return $filterValues[1];
            }
        }

        return 0;
    }

    /**
     * @return integer
     */
    protected function getOffset()
    {
        return $this->getRequest()->request->get(self::PARAM_OFFSET, 0);
    }

    /**
     * @return string[]
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_EXCLUDE_CONTENT_OBJECT_IDS);
    }

    /**
     * @return string
     */
    protected function getSearchQuery()
    {
        return $this->getRequest()->request->get(self::PARAM_SEARCH_QUERY);
    }

    /**
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    protected function retrieveAllCategories()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class_name(), RepositoryCategory::PROPERTY_TYPE_ID),
            new StaticConditionVariable($this->getUser()->getId())
        );

        return DataManager::retrieve_categories($condition);
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory[]
     */
    protected function retrieveCategories()
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class_name(), RepositoryCategory::PROPERTY_PARENT),
            new StaticConditionVariable($this->getFilter())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class_name(), RepositoryCategory::PROPERTY_TYPE_ID),
            new StaticConditionVariable($this->getUser()->getId())
        );

        return DataManager::retrieve_categories(new AndCondition($conditions))->as_array();
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject[]
     */
    protected function retrieveContentObjects()
    {
        $parameters = new DataClassRetrievesParameters(
            $this->getContentObjectConditions(), 100, $this->getOffset(), array(
                new OrderBy(
                    new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_TITLE)
                )
            )
        );

        return DataManager::retrieve_active_content_objects(ContentObject::class_name(), $parameters)->as_array();
    }
}
