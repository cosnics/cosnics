<?php
namespace Chamilo\Core\Repository\Quota\Service;

use Chamilo\Core\User\Service\UserService;
use Chamilo\Libraries\Storage\Exception\DataClassNoResultException;

/**
 * @package Chamilo\Core\Repository\Quota\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AggregatedUserStorageSpaceCalculator implements AggregatedUserStorageSpaceCalculatorInterface
{
    protected UserService $userService;

    protected UserStorageSpaceCalculatorInterface $userStorageSpaceCalculator;

    public function __construct(
        UserService $userService, UserStorageSpaceCalculatorInterface $userStorageSpaceCalculator
    )
    {
        $this->userService = $userService;
        $this->userStorageSpaceCalculator = $userStorageSpaceCalculator;
    }

    public function getMaximumAggregatedUserStorageSpace(): int
    {
        try
        {
            $totalQuota = 0;
            $users = $this->getUserService()->findUsers();

            foreach ($users as $user)
            {
                $totalQuota += $this->getUserStorageSpaceCalculator()->getAllowedStorageSpaceForUser($user);
            }

            return $totalQuota;
        }
        catch (DataClassNoResultException $exception)
        {
            return 0;
        }
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    public function getUserStorageSpaceCalculator(): UserStorageSpaceCalculatorInterface
    {
        return $this->userStorageSpaceCalculator;
    }
}