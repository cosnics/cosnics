<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Integration\Chamilo\Application\Calendar\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Integration\Chamilo\Application\Calendar\Repository\CalendarEventDataProviderRepository;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Core\Repository\Publication\Storage\Repository\PublicationRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Translation\Translation;

/**
 * Calendar Event Data Provider for the assignment tool
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CalendarEventDataProvider
    extends \Chamilo\Application\Weblcms\Integration\Chamilo\Application\Calendar\Service\CalendarEventDataProvider
{

    /**
     * Returns the context for the calendar
     *
     * @return string
     */
    public function getCalendarContext()
    {
        return Manager::CONTEXT;
    }

    /**
     * Returns the name for the calendar
     *
     * @return string
     */
    public function getCalendarName()
    {
        return Translation::getInstance()->getTranslation(
            'CoursesAssignmentCalendar', null,
            'Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Integration\Chamilo\Application\Calendar'
        );
    }

    /**
     * Retrieves the valid publications for the user
     *
     * @param User $user
     * @param int $fromDate
     * @param int $toDate
     *
     * @return ContentObjectPublication[]
     */
    public function getPublications(User $user, $fromDate, $toDate)
    {
        $courses = DataManager::retrieve_all_courses_from_user($user);

        return $this->getRepository()->getPublications($fromDate, $toDate, $courses);
    }

    /**
     * @return CalendarEventDataProviderRepository
     */
    protected function getRepository()
    {
        return new CalendarEventDataProviderRepository(new PublicationRepository());
    }
}