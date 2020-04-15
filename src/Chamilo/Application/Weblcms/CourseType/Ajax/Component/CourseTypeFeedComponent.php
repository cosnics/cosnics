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
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class CourseTypeFeedComponent extends Manager
{
    const PARAM_EXCLUDE_COURSE_TYPE_IDS = 'exclude_course_type_ids';
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
        $result->set_property(self::PROPERTY_TOTAL_ELEMENTS, $this->countCourseTypes());

        $result->display();
    }

    function contains_results($objects)
    {
        if (count($objects))
        {
            return true;
        }

        return false;
    }

    /**
     * @return integer
     */
    public function countCourseTypes()
    {
        return DataManager::count(CourseType::class, new DataClassCountParameters($this->getCourseTypeConditions()));
    }

    function dump_tree($course_types)
    {
        if ($this->contains_results($course_types))
        {
            $glyph = new FontAwesomeGlyph('folder', array('unlinked'), null, 'fas');
            echo '<node id="coursetype" classes="' . $glyph->getClassNamesString() . '" title="Coursetypes">', PHP_EOL;
            $glyph = new FontAwesomeGlyph('layer-group', array(), null, 'fas');

            foreach ($course_types as $index => $course_type)
            {
                echo '<leaf id="coursetype_' . $index . '" classes="' . $glyph->getClassNamesString() . '" title="' .
                    htmlentities($course_type) . '" description="' . htmlentities($course_type) . '"/>' . PHP_EOL;
            }

            echo '</node>', PHP_EOL;
        }
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    public function getCourseTypeConditions()
    {
        $excludedCourseTypeIdentifiers = $this->getExcludedCourseTypeIdentifiers();
        $searchQuery = $this->getSearchQuery();

        $conditions = array();

        if (!empty($searchQuery))
        {
            $conditions[] = Utilities::query_to_condition(
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

    public function getElements()
    {
        $elements = new AdvancedElementFinderElements();
        $folderGlyph = new FontAwesomeGlyph('folder', array(), null, 'fas');
        $courseTypeGlyph = new FontAwesomeGlyph('layer-group', array(), null, 'fas');
        $courseTypesTitle = $this->getTranslator()->trans('Coursetypes', array(), 'Chamilo\Application\Weblcms');
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
     * @return \Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType[]
     */
    public function retrieveCourseTypes()
    {
        $parameters = new DataClassRetrievesParameters(
            $this->getCourseTypeConditions(), null, null,
            array(new OrderBy(new PropertyConditionVariable(CourseType::class, CourseType::PROPERTY_TITLE)))
        );

        return DataManager::retrieves(CourseType::class_name(), $parameters)->as_array();
    }

    public function runOld()
    {
        $query = Request::get('query');
        $exclude = Request::get('exclude');

        $course_type_conditions = array();

        if ($query)
        {
            $condition_properties = array();
            $condition_properties[] = new PropertyConditionVariable(
                CourseType::class_name(), CourseType::PROPERTY_TITLE
            );

            $course_type_conditions[] = Utilities::query_to_condition($query, $condition_properties);
        }

        if ($exclude)
        {
            if (!is_array($exclude))
            {
                $exclude = array($exclude);
            }

            $exclude_conditions = array();
            $exclude_conditions['coursetype'] = array();

            foreach ($exclude as $id)
            {
                $id = explode('_', $id);

                if ($id[0] == 'coursetype')
                {
                    $condition = new NotCondition(
                        new EqualityCondition(
                            new PropertyConditionVariable(CourseType::class_name(), CourseType::PROPERTY_ID),
                            new StaticConditionVariable($id[1])
                        )
                    );
                }

                $exclude_conditions[$id[0]][] = $condition;
            }

            if (count($exclude_conditions['coursetype']) > 0)
            {
                $course_type_conditions[] = new AndCondition($exclude_conditions['coursetype']);
            }
        }
        $course_type_conditions[] = new EqualityCondition(
            new PropertyConditionVariable(CourseType::class_name(), CourseType::PROPERTY_ACTIVE),
            new StaticConditionVariable(1)
        );
        $course_type_condition = new AndCondition($course_type_conditions);

        $course_types = array();

        $parameters = new DataClassRetrievesParameters(
            $course_type_condition, null, null,
            array(new OrderBy(new PropertyConditionVariable(CourseType::class_name(), CourseType::PROPERTY_TITLE)))
        );

        $course_types_result_set = DataManager::retrieves(CourseType::class_name(), $parameters);

        while ($course_type = $course_types_result_set->next_result())
        {
            $course_types[$course_type->get_id()] = $course_type->get_title();
        }

        $course_types[0] = Translation::get('NoCourseType', null, __NAMESPACE__);

        header('Content-Type: text/xml');
        echo '<?xml version="1.0" encoding="iso-8859-1"?>', PHP_EOL, '<tree>', PHP_EOL;

        $this->dump_tree($course_types);

        echo '</tree>';
    }
}