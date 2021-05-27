<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Application\Calendar\Repository;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Publication\Storage\Repository\PublicationRepository;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Repository to retrieve calendar events for the assignment tool based on the due date of assignments
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class CalendarEventDataProviderRepository
{

    /**
     *
     * @var PublicationRepository
     */
    protected $publicationRepository;

    /**
     * CalendarEventDataProviderRepository constructor.
     * 
     * @param PublicationRepository $publicationRepository
     */
    public function __construct(PublicationRepository $publicationRepository)
    {
        $this->publicationRepository = $publicationRepository;
    }

    /**
     * Retrieves the valid publications for the user
     * 
     * @param Course[]   $courses
     * @param int $fromDate
     * @param int $toDate
     *
     * @return ContentObjectPublication[]
     */
    function getPublications($fromDate, $toDate, $courses = [])
    {
        $parameters = new RecordRetrievesParameters(null, $this->getPublicationsCondition($fromDate, $toDate, $courses));
        
        return $this->publicationRepository->getPublicationsWithContentObjects(
            $parameters, 
            ContentObjectPublication::class,
            $this->getContentObjectClassName());
    }

    /**
     * Retrieves the conditions to retrieve the publications
     * 
     * @param Course[]   $courses
     * @param int $fromDate
     * @param int $toDate
     *
     * @return Condition
     */
    protected function getPublicationsCondition($fromDate, $toDate, $courses = [])
    {
        $courseIds = [];
        foreach ($courses as $course)
        {
            $courseIds[] = $course->getId();
        }
        
        $conditions = [];
        
        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class,
                ContentObjectPublication::PROPERTY_COURSE_ID), 
            $courseIds);
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class,
                ContentObjectPublication::PROPERTY_TOOL), 
            new StaticConditionVariable($this->getToolName()));
        
        $conditions[] = $this->getSpecificContentObjectConditions($fromDate, $toDate);
        
        return new AndCondition($conditions);
    }

    /**
     *
     * @return string
     */
    abstract protected function getToolName();

    /**
     *
     * @return string
     */
    abstract protected function getContentObjectClassName();

    /**
     *
     * @param int $fromDate
     * @param int $toDate
     *
     * @return Condition
     */
    abstract protected function getSpecificContentObjectConditions($fromDate, $toDate);
}