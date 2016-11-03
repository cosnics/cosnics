<?php

namespace Chamilo\Application\Weblcms\Course\Ajax\Component;

use Chamilo\Application\Weblcms\Course\Ajax\Manager;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElement;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\Ajax\AjaxResultDataProviderInterface;
use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\Ajax\AjaxResultGenerator;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\ResultSet\ResultSet;

/**
 * Returns the courses formatted for the element finder
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GetCoursesForElementFinderComponent extends Manager implements AjaxResultDataProviderInterface
{
    const PARAM_SEARCH_QUERY = 'query';
    const PARAM_OFFSET = 'offset';

    /**
     * @var AjaxResultGenerator
     */
    protected $ajaxResultGenerator;

    /**
     * Runs this component and returns it's response
     */
    public function run()
    {
        $this->ajaxResultGenerator = new AjaxResultGenerator(
            $this, $this->getRequest()->get(self::PARAM_SEARCH_QUERY), $this->getRequest()->get(self::PARAM_OFFSET)
        );
        
        $this->ajaxResultGenerator->generateAjaxResult()->display();
    }

    /**
     * Generates the elements for the advanced element finder
     *
     * @param AdvancedElementFinderElements $advancedElementFinderElements
     */
    public function generateElements(AdvancedElementFinderElements $advancedElementFinderElements)
    {
        $courses = $this->getCourses();
        if ($courses)
        {
            /** @var Course $course */
            while ($course = $courses->next_result())
            {
                $advancedElementFinderElements->add_element(
                    new AdvancedElementFinderElement(
                        'course_' . $course->getId(),
                        'type type_course',
                        $course->get_title(),
                        $course->get_visual_code()
                    )
                );
            }
        }
    }

    /**
     * Returns the number of total elements (without the offset)
     *
     * @return int
     */
    public function getTotalNumberOfElements()
    {
        return \Chamilo\Application\Weblcms\Course\Storage\DataManager::count(
            Course::class_name(), $this->getCondition()
        );
    }

    /**
     * Retrieves the courses for the current request
     *
     * @return ResultSet
     */
    protected function getCourses()
    {
        $parameters = new DataClassRetrievesParameters(
            $this->getCondition(), 100, $this->ajaxResultGenerator->getOffset(),
            array(
                new OrderBy(new PropertyConditionVariable(Course:: class_name(), Course :: PROPERTY_TITLE)),
            )
        );

        return \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieves(Course::class_name(), $parameters);
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    protected function getCondition()
    {
        return $this->ajaxResultGenerator->getSearchCondition(
            array(
                new PropertyConditionVariable(Course::class_name(), Course::PROPERTY_TITLE),
                new PropertyConditionVariable(Course::class_name(), Course::PROPERTY_VISUAL_CODE),
            )
        );
    }
}