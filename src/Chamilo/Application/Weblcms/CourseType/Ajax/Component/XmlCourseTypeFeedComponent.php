<?php
namespace Chamilo\Application\Weblcms\CourseType\Ajax\Component;

use Chamilo\Application\Weblcms\CourseType\Ajax\Manager;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\CourseType\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class XmlCourseTypeFeedComponent extends Manager
{

    public function run()
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

    function contains_results($objects)
    {
        if (count($objects))
        {
            return true;
        }

        return false;
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
}