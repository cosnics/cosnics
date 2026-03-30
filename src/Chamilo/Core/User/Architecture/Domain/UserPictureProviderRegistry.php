<?php
namespace Chamilo\Core\User\Architecture\Domain;

use Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Architecture\Domain
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserPictureProviderRegistry extends ArrayCollection
{
    public function __construct(
        protected readonly Translator $translator, protected readonly string $activePictureProviderClass
    )
    {
        parent::__construct();
    }

    public function addAvailablePictureProvider(UserPictureProviderInterface $userPictureProvider): void
    {
        $this->set(get_class($userPictureProvider), $userPictureProvider);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function getActivePictureProvider(): UserPictureProviderInterface
    {
        $configuredPictureProvider = $this->activePictureProviderClass;

        if (!$this->containsKey($configuredPictureProvider)) {
            throw new NoSuchClassException($configuredPictureProvider, UserPictureProviderInterface::class);
        }

        return $this->get($configuredPictureProvider);
    }

    /**
     * @return string[]
     */
    public function getAvailablePictureProviderTypes(): array
    {
        return $this->getKeys();
    }
}
