<?php

namespace Chamilo\Application\ExamAssignment\Repository;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\FixedPropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Class ExamAssignmentRepository
 * @package Chamilo\Application\ExamAssignment\Repository
 */
class ExamAssignmentRepository
{
    /**
     * @var DataClassRepository
     */
    protected $dataClassRepository;

    /**
     * TreeNodeDataRepository constructor.
     *
     * @param DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @param array $courseIds
     *
     * SELECT from_unixtime(start_time), from_unixtime(end_time), rco.title, wcop.course_id FROM `repository_assignment` ra
     * JOIN `repository_content_object` rco on rco.id = ra.id JOIN `weblcms_content_object_publication` wcop on wcop.content_object_id = ra.id
     * WHERE 1591776000 BETWEEN start_time and end_time and wcop.course_id in (33718, 38885, 13027) and wcop.tool='Assignment'
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function getCurrentExamAssignmentsInCourses(array $courseIds = [])
    {
        $now = time();

        $conditions = [];

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(Assignment::class, Assignment::PROPERTY_START_TIME),
            ComparisonCondition::LESS_THAN_OR_EQUAL,
            new StaticConditionVariable($now)
        );

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(Assignment::class, Assignment::PROPERTY_END_TIME),
            ComparisonCondition::GREATER_THAN_OR_EQUAL,
            new StaticConditionVariable($now - (3600 * 8))
        );

        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_COURSE_ID
            ),
            $courseIds
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObjectPublication::class, ContentObjectPublication::PROPERTY_TOOL),
            new StaticConditionVariable('ExamAssignment')
        );

        $condition = new AndCondition($conditions);

        $joins = new Joins();

        $joins->add(
            new Join(
                ContentObject::class,
                new EqualityCondition(
                    new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID),
                    new PropertyConditionVariable(Assignment::class, Assignment::PROPERTY_ID)
                )
            )
        );

        $joins->add(
            new Join(
                ContentObjectPublication::class,
                new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication::class, ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID
                    ),
                    new PropertyConditionVariable(Assignment::class, Assignment::PROPERTY_ID)
                )
            )
        );

        $joins->add(
            new Join(
                Course::class,
                new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObjectPublication::class, ContentObjectPublication::PROPERTY_COURSE_ID
                    ),
                    new PropertyConditionVariable(Course::class, Course::PROPERTY_ID)
                )
            )
        );

        $joins->add(
            new Join(
                User::class,
                new EqualityCondition(
                    new PropertyConditionVariable(Course::class, Course::PROPERTY_TITULAR_ID),
                    new PropertyConditionVariable(User::class, User::PROPERTY_ID)
                )
            )
        );

        $properties = new DataClassProperties();
        $properties->add(
            new FixedPropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_ID, 'publication_id'
            )
        );

        $properties->add(
            new PropertyConditionVariable(ContentObjectPublication::class, ContentObjectPublication::PROPERTY_COURSE_ID)
        );

        $properties->add(new PropertyConditionVariable(Assignment::class, Assignment::PROPERTY_START_TIME));
        $properties->add(new PropertyConditionVariable(Assignment::class, Assignment::PROPERTY_END_TIME));
        $properties->add(new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TITLE));
        $properties->add(new FixedPropertyConditionVariable(Course::class, Course::PROPERTY_TITLE, 'course_title'));
        $properties->add(new PropertyConditionVariable(Course::class, Course::PROPERTY_VISUAL_CODE));
        $properties->add(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME));
        $properties->add(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME));

        $parameters = new RecordRetrievesParameters($properties, $condition, null, null, [], $joins);

        return $this->dataClassRepository->records(Assignment::class, $parameters);
    }
}
