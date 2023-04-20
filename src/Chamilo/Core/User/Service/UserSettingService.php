<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Exception;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * @package Chamilo\Core\User\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class UserSettingService
{
    protected UserService $userService;

    protected FilesystemAdapter $userSettingsCache;

    /**
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Symfony\Component\Cache\Adapter\FilesystemAdapter $userSettingsCache
     */
    public function __construct(UserService $userService, FilesystemAdapter $userSettingsCache)
    {
        $this->userService = $userService;
        $this->userSettingsCache = $userSettingsCache;
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

