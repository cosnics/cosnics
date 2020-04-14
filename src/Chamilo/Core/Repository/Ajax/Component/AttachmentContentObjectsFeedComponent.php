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
        foreach ($this->retrieveCategories() as $category)
        {
            $myRepositoryElement->add_child(
                $this->getCategoryAdvancedElementFinderElement($category, AdvancedElementFinderElement::TYPE_FILTER)
            );
        }
    }

    /**
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement $myRepositoryElement
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function addContentObjectElement(AdvancedElementFinderElement $myRepositoryElement)
    {
        $this->addContentObjectsToParentElement($myRepositoryElement, $this->retrieveContentObjects());
    }

    /**
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement $parentElement
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject[] $contentObjects
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function addContentObjectsToParentElement(
        AdvancedElementFinderElement $parentElement, array $contentObjects
    )
    {
        foreach ($contentObjects as $contentObject)
        {
            $parentElement->add_child(
                $this->getContentObjectAdvancedElementFinderElement($contentObject)
            );
        }
    }

    /**
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement $parentElement
     * @param \stdClass $searchResult
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function addRecursiveElement(AdvancedElementFinderElement $parentElement, stdClass $searchResult)
    {
        $categoryElement = $this->getCategoryAdvancedElementFinderElement(
            $searchResult->category, AdvancedElementFinderElement::TYPE_VISUAL
        );

        $parentElement->add_child($categoryElement);

        foreach ($searchResult->subCategories as $subCategory)
        {
            $this->addRecursiveElement($categoryElement, $subCategory);
        }

        $this->addContentObjectsToParentElement($categoryElement, $searchResult->contentObjects);
    }

    /**
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements $elements
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function addSearchQueryContentObjectElement(AdvancedElementFinderElements $elements)
    {
        $contentObjects = $this->retrieveContentObjects();
        $searchResults = array();
        $filter = $this->getFilter();

        foreach ($contentObjects as $contentObject)
        {
            $repositoryCategory = $this->retrieveRepositoryCategoryByIdentifier($contentObject->get_parent_id());

            $repositoryCategoryIdentifiers =
                ($contentObject->get_parent_id() != 0 ? $repositoryCategory->get_parent_ids() : array());

            $repositoryCategoryIdentifiers[] = $contentObject->get_parent_id();

            $previousParent = null;

            foreach ($repositoryCategoryIdentifiers as $repositoryCategoryIdentifier)
            {
                if (!array_key_exists($repositoryCategoryIdentifier, $searchResults))
                {
                    $repositoryCategory = $this->retrieveRepositoryCategoryByIdentifier($repositoryCategoryIdentifier);

                    $searchResult = new stdClass();

                    $searchResult->category = $repositoryCategory;
                    $searchResult->subCategories = array();
                    $searchResult->contentObjects = array();

                    $searchResults[$repositoryCategoryIdentifier] = $searchResult;
                }

                if (!is_null($previousParent))
                {
                    if (!array_key_exists(
                        $repositoryCategoryIdentifier, $searchResults[$previousParent]->subCategories
                    ))
                    {
                        $searchResults[$previousParent]->subCategories[$repositoryCategoryIdentifier] =
                            $searchResults[$repositoryCategoryIdentifier];
                    }
                }

                $previousParent = $repositoryCategoryIdentifier;
            }

            $searchResults[$contentObject->get_parent_id()]->contentObjects[$contentObject->getId()] = $contentObject;
        }

        $rootResult = $searchResults[$filter];

        $categoryElement = $this->getCategoryAdvancedElementFinderElement(
            $rootResult->category, AdvancedElementFinderElement::TYPE_VISUAL
        );

        foreach ($rootResult->subCategories as $subCategory)
        {
            $this->addRecursiveElement($categoryElement, $subCategory);
        }

        $this->addContentObjectsToParentElement($categoryElement, $rootResult->contentObjects);

        $elements->add_element($categoryElement);
    }

    /**
     * @return integer
     */
    protected function countContentObjects()
    {
        return DataManager::count_active_content_objects(
            ContentObject::class, new DataClassCountParameters($this->getContentObjectConditions())
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory $category
     * @param integer $elementType
     *
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement
     */
    protected function getCategoryAdvancedElementFinderElement(RepositoryCategory $category, int $elementType)
    {
        $glyph = new FontAwesomeGlyph(($category->getId() == 0 ? 'hdd' : 'folder'), array('fa-fw'), null, 'fas');

        return new AdvancedElementFinderElement(
            'category_' . $category->getId(), $glyph->getClassNamesString(), $category->get_name(),
            $category->get_name(), $elementType
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

            $category = DataManager::retrieve_by_id(RepositoryCategory::class, $this->getFilter());

            if ($category instanceof RepositoryCategory)
            {
                $categoryIdentifiers = $category->get_children_ids();
                $categoryIdentifiers[] = $category->getId();

                return $categoryIdentifiers;
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
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function getContentObjectAdvancedElementFinderElement(ContentObject $contentObject)
    {
        return new AdvancedElementFinderElement(
            'content_object_' . $contentObject->getId(),
            $contentObject->getGlyph(IdentGlyph::SIZE_MINI, true, array('fa-fw'))->getClassNamesString(),
            $contentObject->get_title(), $contentObject->get_type_string()
        );
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function getContentObjectConditions()
    {
        $excludedContentObjectIdentifiers = $this->getExcludedContentObjectIdentifiers();
        $searchQuery = $this->getSearchQuery();

        $conditions = array();

        if (!empty($searchQuery))
        {
            $conditions[] = Utilities::query_to_condition(
                $searchQuery, array(new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TITLE))
            );
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OWNER_ID),
            new StaticConditionVariable($this->getUser()->getId())
        );

        $categoryIdentifiers = $this->getCategoryIdentifiers();

        if (count($categoryIdentifiers) > 0)
        {
            $conditions[] = new InCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_PARENT_ID),
                $categoryIdentifiers
            );
        }

        $conditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_STATE),
                new StaticConditionVariable(ContentObject::STATE_RECYCLED)
            )
        );

        if (is_array($excludedContentObjectIdentifiers) && count($excludedContentObjectIdentifiers) > 0)
        {
            $conditions[] = new NotCondition(
                new InCondition(
                    new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID),
                    $excludedContentObjectIdentifiers
                )
            );
        }

        $conditions[] = new InCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TYPE),
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

        if ($this->getSearchQuery())
        {
            $this->addSearchQueryContentObjectElement($elements);
        }
        else
        {
            $category = $this->retrieveRepositoryCategoryByIdentifier($this->getFilter());
            $rootElement =
                $this->getCategoryAdvancedElementFinderElement($category, AdvancedElementFinderElement::TYPE_VISUAL);

            $this->addCategoryElement($rootElement);
            $this->addContentObjectElement($rootElement);

            if ($rootElement->hasChildren())
            {
                $elements->add_element($rootElement);
            }
        }

        return $elements->as_array();
    }

    /**
     * @return string
     */
    protected function getExcludedContentObjectIdentifiers()
    {
        return $this->getRequest()->request->get(self::PARAM_EXCLUDE_CONTENT_OBJECT_IDS);
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
     * @return string
     */
    protected function getSearchQuery()
    {
        return $this->getRequest()->request->get(self::PARAM_SEARCH_QUERY);
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory[]
     */
    protected function retrieveCategories()
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class, RepositoryCategory::PROPERTY_PARENT),
            new StaticConditionVariable($this->getFilter())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class, RepositoryCategory::PROPERTY_TYPE_ID),
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
                    new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TITLE)
                )
            )
        );

        return DataManager::retrieve_active_content_objects(ContentObject::class, $parameters)->as_array();
    }

    /**
     * @param integer $repositoryCategoryByIdentifier
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory
     */
    protected function retrieveRepositoryCategoryByIdentifier(int $repositoryCategoryByIdentifier)
    {
        if ($repositoryCategoryByIdentifier != 0)
        {
            $category = DataManager::retrieve_by_id(RepositoryCategory::class, $repositoryCategoryByIdentifier);
        }
        else
        {
            $category = new RepositoryCategory();
            $category->setId($repositoryCategoryByIdentifier);
            $category->set_name(
                $this->getTranslator()->trans('MyRepository', array(), Utilities::COMMON_LIBRARIES)
            );
        }

        return $category;
    }
}
