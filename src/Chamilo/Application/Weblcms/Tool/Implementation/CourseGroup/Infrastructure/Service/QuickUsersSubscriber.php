<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Domain\QuickUserSubscriberStatus;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class QuickUsersSubscriber
{
    /**
     * @var CourseGroupService
     */
    protected $courseGroupService;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var CourseService
     */
    protected $courseService;

    /**
     * QuickUsersSubscriber constructor.
     *
     * @param CourseGroupService $courseGroupService
     * @param UserService $userService
     * @param CourseService $courseService
     */
    public function __construct(
        CourseGroupService $courseGroupService, UserService $userService, CourseService $courseService
    )
    {
        $this->courseGroupService = $courseGroupService;
        $this->userService = $userService;
        $this->courseService = $courseService;
    }

    /**
     * @param Course $course
     * @param CourseGroup $courseGroup
     * @param string $userIdentifiersCSV
     *
     * @return QuickUserSubscriberStatus[]
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function subscribeUsersFromCSVFormat(Course $course, CourseGroup $courseGroup, string $userIdentifiersCSV)
    {
        $userIdentifiers = $this->parseUserIdentifiersFromCSV($userIdentifiersCSV);

        /** @var User[] $usersToSubscribe */
        $usersToSubscribe = $userStatuses = [];

        $processedUserIds = [];

        foreach ($userIdentifiers as $userIdentifier)
        {
            $this->prepareUser($userIdentifier, $course, $courseGroup, $usersToSubscribe, $userStatuses);
        }

        // preparation and subscription was splitted because users could be retrieved in batch using extensive methods
        // however every user has to be logged so every user has to be prepared separately and these two loops could
        // be refactored into one

        foreach ($usersToSubscribe as $userIdentifier => $userToSubscribe)
        {
            // yes people subscribe others with several different user identifiers twice or three times
            if(array_key_exists($userToSubscribe->getId(), $processedUserIds))
            {
                continue;
            }

            $this->subscribeUser($userToSubscribe, $courseGroup, $userIdentifier, $userStatuses);

            $processedUserIds[$userToSubscribe->getId()] = true;
        }

        $this->courseGroupService->recalculateMaxMembers($courseGroup);

        return $userStatuses;
    }

    /**
     * @param string $userIdentifiersCSV
     *
     * @return array
     */
    protected function parseUserIdentifiersFromCSV(string $userIdentifiersCSV): array
    {
        $delimiters = [',', ';', PHP_EOL];
        $userIdentifiersCSV = str_replace($delimiters, ',', $userIdentifiersCSV);

        $userIdentifierParts = explode(',', $userIdentifiersCSV);
        $userIdentifiers = [];

        foreach($userIdentifierParts as $userIdentifierPart)
        {
            $userIdentifiers[] = trim($userIdentifierPart);
        }

        return $userIdentifiers;
    }

    /**
     * @param string $userIdentifier
     * @param Course $course
     * @param CourseGroup $courseGroup
     * @param User[] $usersToSubscribe
     * @param array $userStatuses
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    protected function prepareUser(
        string $userIdentifier, Course $course, CourseGroup $courseGroup, array &$usersToSubscribe, array &$userStatuses
    )
    {
        $user = $this->userService->getUserByUsernameOfficialCodeOrEmail($userIdentifier);
        if (!$user instanceof User)
        {
            $userStatuses[] =
                new QuickUserSubscriberStatus(
                    $userIdentifier, QuickUserSubscriberStatus::STATUS_USER_NOT_FOUND, null, $courseGroup
                );

            return;
        }

        if (!$this->courseService->isUserSubscribedToCourse($user, $course))
        {
            $userStatuses[] = new QuickUserSubscriberStatus(
                $userIdentifier, QuickUserSubscriberStatus::STATUS_USER_NOT_SUBSCRIBED_IN_COURSE, $user, $courseGroup
            );

            return;
        }

        $usersToSubscribe[$userIdentifier] = $user;
    }

    /**
     * @param User $user
     * @param CourseGroup $courseGroup
     * @param string $userIdentifier
     * @param $userStatuses
     */
    protected function subscribeUser(User $user, CourseGroup $courseGroup, string $userIdentifier, &$userStatuses)
    {
        try
        {
            $this->courseGroupService->subscribeUsersWithoutMaxCapacityCheck($courseGroup, [$user], false);
            $status = QuickUserSubscriberStatus::STATUS_SUCCESS;
        }
        catch (\Exception $ex)
        {
            $status = QuickUserSubscriberStatus::STATUS_UNKNOWN_ERROR;
        }

        $userStatuses[] = new QuickUserSubscriberStatus($userIdentifier, $status, $user, $courseGroup);
    }

}
