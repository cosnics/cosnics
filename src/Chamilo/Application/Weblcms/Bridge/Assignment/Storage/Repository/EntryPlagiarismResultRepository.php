<?php

namespace Chamilo\Application\Weblcms\Bridge\Assignment\Storage\Repository;

use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\EntryPlagiarismResult;
use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Score;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\FilterParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntryPlagiarismResultRepository extends
    \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\Repository\EntryPlagiarismResultRepository
{
    /**
     * @return string
     */
    protected function getEntryPlagiarismResultClass()
    {
        return EntryPlagiarismResult::class;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findUserEntriesWithPlagiarismResult(
        ContentObjectPublication $contentObjectPublication, FilterParameters $filterParameters
    )
    {
        $properties = new DataClassProperties();

        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_OFFICIAL_CODE));

        return $this->findEntriesWithPlagiarismResult(
            Entry::ENTITY_TYPE_USER,
            $properties,
            User::class_name(),
            $this->getTargetBaseVariable(User::class_name()),
            $this->getContentObjectPublicationCondition($contentObjectPublication),
            $filterParameters
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return int
     */
    public function countUserEntriesWithPlagiarismResult(
        ContentObjectPublication $contentObjectPublication, FilterParameters $filterParameters
    )
    {
        $properties = new DataClassProperties();

        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_OFFICIAL_CODE));

        return $this->countEntriesWithPlagiarismResult(
            Entry::ENTITY_TYPE_USER,
            $properties,
            User::class_name(),
            $this->getTargetBaseVariable(User::class_name()),
            $this->getContentObjectPublicationCondition($contentObjectPublication),
            $filterParameters
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findCourseGroupEntriesWithPlagiarismResult(
        ContentObjectPublication $contentObjectPublication, FilterParameters $filterParameters
    )
    {
        $properties = new DataClassProperties();
        $properties->add(new PropertyConditionVariable(CourseGroup::class, CourseGroup::PROPERTY_NAME));

        return $this->findEntriesWithPlagiarismResult(
            Entry::ENTITY_TYPE_COURSE_GROUP,
            $properties,
            CourseGroup::class_name(),
            $this->getTargetBaseVariable(CourseGroup::class_name()),
            $this->getContentObjectPublicationCondition($contentObjectPublication),
            $filterParameters
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return int
     */
    public function countCourseGroupEntriesWithPlagiarismResult(
        ContentObjectPublication $contentObjectPublication, FilterParameters $filterParameters
    )
    {
        $properties = new DataClassProperties();
        $properties->add(new PropertyConditionVariable(CourseGroup::class, CourseGroup::PROPERTY_NAME));

        return $this->countEntriesWithPlagiarismResult(
            Entry::ENTITY_TYPE_COURSE_GROUP,
            $properties,
            CourseGroup::class_name(),
            $this->getTargetBaseVariable(CourseGroup::class_name()),
            $this->getContentObjectPublicationCondition($contentObjectPublication),
            $filterParameters
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findPlatformGroupEntriesWithPlagiarismResult(
        ContentObjectPublication $contentObjectPublication, FilterParameters $filterParameters
    )
    {
        $properties = new DataClassProperties();
        $properties->add(new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME));

        return $this->findEntriesWithPlagiarismResult(
            Entry::ENTITY_TYPE_PLATFORM_GROUP,
            $properties,
            Group::class_name(),
            $this->getTargetBaseVariable(Group::class_name()),
            $this->getContentObjectPublicationCondition($contentObjectPublication),
            $filterParameters
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return int
     */
    public function countPlatformGroupEntriesWithPlagiarismResult(
        ContentObjectPublication $contentObjectPublication, FilterParameters $filterParameters
    )
    {
        $properties = new DataClassProperties();
        $properties->add(new PropertyConditionVariable(Group::class, Group::PROPERTY_NAME));

        return $this->countEntriesWithPlagiarismResult(
            Entry::ENTITY_TYPE_PLATFORM_GROUP,
            $properties,
            Group::class_name(),
            $this->getTargetBaseVariable(Group::class_name()),
            $this->getContentObjectPublicationCondition($contentObjectPublication),
            $filterParameters
        );
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    protected function getContentObjectPublicationCondition(ContentObjectPublication $contentObjectPublication)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                $this->getEntryClassName(),
                Entry::PROPERTY_CONTENT_OBJECT_PUBLICATION_ID
            ),
            new StaticConditionVariable($contentObjectPublication->getId())
        );
    }

    /**
     * @return string
     */
    protected function getEntryClassName()
    {
        return Entry::class;
    }

    /**
     * @return string
     */
    protected function getScoreClassName()
    {
        return Score::class;
    }

    /**
     * @return string
     */
    protected function getEntryPlagiarismResultClassName()
    {
        return EntryPlagiarismResult::class;
    }
}
