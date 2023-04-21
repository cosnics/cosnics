<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Exception;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * @package Chamilo\Core\User\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class UserSettingService
{
    protected DatetimeUtilities $datetimeUtilities;

    protected UserService $userService;

    protected FilesystemAdapter $userSettingsCache;

    public function __construct(
        UserService $userService, FilesystemAdapter $userSettingsCache, DatetimeUtilities $datetimeUtilities
    )
    {
        $this->userService = $userService;
        $this->userSettingsCache = $userSettingsCache;
        $this->datetimeUtilities = $datetimeUtilities;
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function clearSettingsCacheforUser(User $user): bool
    {
        return $this->getUserSettingsCache()->deleteItem('user.' . $user->getId());
    }

    /**
     * @throws \Exception
     */
    public function convertDateToUserTimezone(User $user, string $date, ?string $format = null): string
    {
        $userTimezone = $this->getSettingForUser($user, 'Chamilo\Core\Admin', 'platform_timezone');

        return $this->getDatetimeUtilities()->convertDateToTimezone($date, $format, $userTimezone);
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    public function getSettingForUser(User $user, string $context, string $variable, bool $useCache = true): ?string
    {
        $settings = $this->getSettingsForUser($user, $useCache);

        return $settings[$context][$variable];
    }

    public function getSettingsForUser(User $user, bool $useCache = true): array
    {
        try
        {
            $userSettingsCache = $this->getUserSettingsCache();
            $userService = $this->getUserService();

            if ($useCache)
            {
                $userSettings = $userSettingsCache->getItem('user.' . $user->getId());

                if (!$userSettings->isHit())
                {
                    $userSettings->set($userService->findSettingsForUser($user));
                    $userSettingsCache->save($userSettings);
                }

                return $userSettings->get();
            }
            else
            {
                return $userService->findSettingsForUser($user);
            }
        }
        catch (Exception $exception)
        {
            return [];
        }
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    public function getUserSettingsCache(): FilesystemAdapter
    {
        return $this->userSettingsCache;
    }

}

