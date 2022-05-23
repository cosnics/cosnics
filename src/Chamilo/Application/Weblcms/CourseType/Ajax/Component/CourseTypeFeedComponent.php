<?php
namespace Chamilo\Application\Weblcms\CourseType\Ajax\Component;

use Chamilo\Application\Weblcms\CourseType\Ajax\Manager;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\CourseType\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator;

class CourseTypeFeedComponent extends Manager
{
    const PARAM_EXCLUDE_COURSE_TYPE_IDS = 'exclude_course_type_ids';
    const PARAM_FILTER = 'filter';
    const PARAM_OFFSET = 'offset';
    const PARAM_SEARCH_QUERY = 'query';

    const PROPERTY_ELEMENTS = 'elements';
    const PROPERTY_TOTAL_ELEMENTS = 'total_elements';

    public function run()
    {
        $result = new JsonAjaxResult();

        $result->set_property(self::PROPERTY_ELEMENTS, $this->getElements());
        $result->set_property(self::PROPERTY_TOTAL_ELEMENTS, $this->countCourseTypes());

        $result->display();
    }

    /**
     * @return integer
     */
    public function countCourseTypes()
    {
        return DataManager::count(CourseType::class, new DataClassCountParameters($this->getCourseTypeConditions()));
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    public function getCourseTypeConditions()
    {
        $excludedCourseTypeIdentifiers = $this->getExcludedCourseTypeIdentifiers();
        $searchQuery = $this->getSearchQuery();

        $conditions = [];

        if (!empty($searchQuery))
        {
            $conditions[] = $this->getSearchQueryConditionGenerator()->getSearchConditions(
                $searchQuery, array(new PropertyConditionVariable(CourseType::class, CourseType::PROPERTY_TITLE))
            );
        }

        if (is_array($excludedCourseTypeIdentifiers) && count($excludedCourseTypeIdentifiers) > 0)
        {
            $conditions[] = new NotCondition(
                new InCondition(
                    new PropertyConditionVariable(ContentObject::class, CourseType::PROPERTY_ID),
                    $excludedCourseTypeIdentifiers
                )
            );
        }

        if (count($conditions) > 0)
        {
            return new AndCondition($conditions);
        }
        else
        {
            return null;
        }
    }

    /**
     * @return string[][]
     */
    public function getElements()
    {
        $elements = new AdvancedElementFinderElements();
        $folderGlyph = new FontAwesomeGlyph('folder', [], null, 'fas');
        $courseTypeGlyph = new FontAwesomeGlyph('layer-group', [], null, 'fas');
        $courseTypesTitle = $this->getTranslator()->trans('Coursetypes', [], 'Chamilo\Application\Weblcms');
        $parentElement = new AdvancedElementFinderElement(
            'course_types', $folderGlyph->getClassNamesString(), $courseTypesTitle, $courseTypesTitle,
            AdvancedElementFinderElement::TYPE_VISUAL
        );

        foreach ($this->retrieveCourseTypes() as $courseType)
        {
            $parentElement->add_child(
                new AdvancedElementFinderElement(
                    'course_type_' . $courseType->getId(), $courseTypeGlyph->getClassNamesString(),
                    $courseType->get_title(), $courseType->get_title(), AdvancedElementFinderElement::TYPE_SELECTABLE
                )
            );
        }

        if ($parentElement->hasChildren())
        {
            $elements->add_element($parentElement);
        }

        return $elements->as_array();
    }

    /**
     * @return string
     */
    protected function getExcludedCourseTypeIdentifiers()
    {
        return $this->getRequest()->request->get(self::PARAM_EXCLUDE_COURSE_TYPE_IDS);
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
     * @return \Chamilo\Libraries\Storage\Service\SearchQueryConditionGenerator
     */
    protected function getSearchQueryConditionGenerator()
    {
        return $this->getService(SearchQueryConditionGenerator::class);
    }

    /**
     * @return \Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType[]
     */
    public function retrieveCourseTypes()
    {
        $parameters = new DataClassRetrievesParameters(
            $this->getCourseTypeConditions(), null, null, new OrderBy(
                array(new OrderProperty(new PropertyConditionVariable(CourseType::class, CourseType::PROPERTY_TITLE)))
            )
        );

        return DataManager::retrieves(CourseType::class, $parameters);
    }
}