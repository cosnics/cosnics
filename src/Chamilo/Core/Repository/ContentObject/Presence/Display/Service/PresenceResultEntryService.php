<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Service;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Chamilo\Libraries\Storage\FilterParameters\FilterParametersBuilder;
use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\Presence;
use Chamilo\Libraries\Architecture\ContextIdentifier;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Service
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class PresenceResultEntryService
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var PresenceService
     */
    protected $presenceService;

    /**
     * @var FilterParametersBuilder
     */
    protected $filterParametersBuilder;

    /**
     * @param UserService $userService
     * @param PresenceService $presenceService
     * @param FilterParametersBuilder $filterParametersBuilder
     */
    public function __construct(UserService $userService, PresenceService $presenceService, FilterParametersBuilder $filterParametersBuilder)
    {
        $this->userService = $userService;
        $this->presenceService = $presenceService;
        $this->filterParametersBuilder = $filterParametersBuilder;
    }

    /**
     * @param Presence $presence
     * @param ContextIdentifier $contextIdentifier
     * @param bool $canUserEditPresence
     * @param bool $createIfNeeded
     *
     * @return array
     */
    public function getResultPeriods(Presence $presence, ContextIdentifier $contextIdentifier, bool $canUserEditPresence, bool $createIfNeeded = false): array
    {
        $periods = $this->presenceService->getResultPeriodsForPresence($presence->getId(), $contextIdentifier);

        if ($createIfNeeded && $canUserEditPresence && count($periods) == 0)
        {
            $period = $this->presenceService->createPresenceResultPeriod($presence, $contextIdentifier);
            $periods = [['date' => $period->getDate(), 'id' => (int) $period->getId()]];
        }
        return $periods;
    }

    /**
     * @param array $userIds
     * @param array $periods
     * @param ContextIdentifier $contextIdentifier
     * @param FilterParameters $filterParameters
     *
     * @return array
     */
    public function getUsers(array $userIds, array $periods, ContextIdentifier $contextIdentifier, FilterParameters $filterParameters): array
    {
        $users = $this->userService->getUsersFromIds($userIds, $contextIdentifier, $filterParameters);

        foreach ($users as $index => $user)
        {
            foreach ($periods as $period)
            {
                $users[$index] = $this->completeUserFields($users[$index], $period['id']);
            }
        }
        return $users;
    }

    /**
     * @param array $user
     * @param int $periodId
     *
     * @return array
     */
    protected function completeUserFields(array $user, int $periodId): array
    {
        $userId = (int) $user['id'];
        $user['id'] = $userId;

        if (!isset($user['photo']))
        {
            $user['photo'] = $this->getProfilePhotoUrl($userId);
        }

        $periodStr = 'period#' . $periodId;
        $statusStr = $periodStr . '-status';
        $checkedInStr = $periodStr . '-checked_in_date';
        $checkedOutStr = $periodStr . '-checked_out_date';

        if (!array_key_exists($statusStr, $user))
        {
            $user[$statusStr] = NULL;
        }

        if (array_key_exists($checkedInStr, $user))
        {
            $user[$checkedInStr] = (int) $user[$checkedInStr];
        }

        if (array_key_exists($checkedOutStr, $user))
        {
            $user[$checkedOutStr] = (int) $user[$checkedOutStr];
        }

        return $user;
    }

    /**
     * @param int $userId
     *
     * @return string
     */
    protected function getProfilePhotoUrl(int $userId): string
    {
        $profilePhotoUrl = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager::ACTION_USER_PICTURE,
                \Chamilo\Core\User\Manager::PARAM_USER_USER_ID => $userId
            )
        );
        return $profilePhotoUrl->getUrl();
    }

    /**
     * @param ChamiloRequest $request
     * @param bool $clear
     *
     * @return FilterParameters
     */
    public function createFilterParameters(ChamiloRequest $request, bool $clear = false): FilterParameters
    {
        $fieldMapper = $this->userService->getFieldMapper();
        $filterParameters = $this->filterParametersBuilder->buildFilterParametersFromRequest($request, $fieldMapper);

        if ($clear)
        {
            return $filterParameters->setCount(null)->setOffset(null);
        }
        return $filterParameters;
    }
}
