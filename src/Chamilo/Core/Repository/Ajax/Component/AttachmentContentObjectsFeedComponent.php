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
     * @var integer
     */
    private $contentObjectCount = 0;

    public function run()
    {
        $result = new JsonAjaxResult();

        $result->set_property(self::PROPERTY_ELEMENTS, $this->getElements());
        $result->set_property(self::PROPERTY_TOTAL_ELEMENTS, $this->contentObjectCount);

        $result->display();
    }

    /**
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements $elements
     */
    protected function addCategoryElement(AdvancedElementFinderElements $elements)
    {
        $glyph = new FontAwesomeGlyph('folder', array('fa-fw'), null, 'fas');

        $myRepository = $this->getTranslator()->trans('Categories', array(), Utilities::COMMON_LIBRARIES);

        $categoriesElement = new AdvancedElementFinderElement(
            'categories', $glyph->getClassNamesString(), $myRepository, $myRepository
        );

        /**
         * @var \Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory[] $category
         */
        $categories = $this->retrieveCategories()->as_array();

        if (count($categories))
        {
            foreach ($categories as $category)
            {
                $categoriesElement->add_child(
                    new AdvancedElementFinderElement(
                        'category_' . $category->getId(), $glyph->getClassNamesString(), $category->get_name(),
                        $category->get_name(), AdvancedElementFinderElement::TYPE_FILTER
                    )
                );
            }

            $elements->add_element($categoriesElement);
        }
    }

    /**
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements $elements
     */
    protected function addContentObjectElement(AdvancedElementFinderElements $elements)
    {
        $glyph = new FontAwesomeGlyph('folder', array('fa-fw'), null, 'fas');

        $myRepository = $this->getTranslator()->trans('ContentObjects', array(), Utilities::COMMON_LIBRARIES);

        $contentObjectElement = new AdvancedElementFinderElement(
            'attachments', $glyph->getClassNamesString(), $myRepository, $myRepository
        );

        /**
         * @var \Chamilo\Core\Repository\Storage\DataClass\ContentObject[] $contentObject
         */
        $contentObjects = $this->retrieveContentObjects()->as_array();

        if (count($contentObjects))
        {
            foreach ($contentObjects as $contentObject)
            {
                $contentObjectElement->add_child(
                    new AdvancedElementFinderElement(
                        'content_object_' . $contentObject->getId(),
                        $contentObject->getGlyph(IdentGlyph::SIZE_MINI, true, array('fa-fw'))->getClassNamesString(),
                        $contentObject->get_title(), $contentObject->get_type_string()
                    )
                );
            }

            $elements->add_element($contentObjectElement);
        }
    }

    protected function addSearchQueryContentObjectElement(AdvancedElementFinderElements $elements)
    {
    }

    /**
     * @return string[][]
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
            $this->addCategoryElement($elements);
            $this->addContentObjectElement($elements);
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

            if (count($filterValues) === 2 && $filterValues[0] == 'category')
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

        return DataManager::retrieve_categories(new AndCondition($conditions));
    }

    /**
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    protected function retrieveContentObjects()
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

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_PARENT_ID),
            new StaticConditionVariable($this->getFilter())
        );

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

        $this->contentObjectCount = DataManager::count_active_content_objects(
            ContentObject::class_name(), new DataClassCountParameters(new AndCondition($conditions))
        );

        $parameters = new DataClassRetrievesParameters(
            new AndCondition($conditions), 100, $this->getOffset(), array(
                new OrderBy(
                    new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_TITLE)
                )
            )
        );

        return DataManager::retrieve_active_content_objects(ContentObject::class_name(), $parameters);
    }
}
