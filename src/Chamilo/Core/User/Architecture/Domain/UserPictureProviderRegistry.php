<?php
namespace Chamilo\Core\User\Architecture\Domain;

use Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Architecture\Domain
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserPictureProviderRegistry extends ArrayCollection
{
    protected string $activePictureProviderClass;

    protected Translator $translator;

    public function __construct(Translator $translator, string $activePictureProviderClass)
    {
        parent::__construct();

        $this->translator = $translator;
        $this->activePictureProviderClass = $activePictureProviderClass;
    }

    public function addAvailablePictureProvider(UserPictureProviderInterface $userPictureProvider): void
    {
        $this->set(get_class($userPictureProvider), $userPictureProvider);
    }

    /**
     * @return \Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface
     * @throws \Exception
     */
    public function getActivePictureProvider(): UserPictureProviderInterface
    {
        $configuredPictureProvider = $this->getActivePictureProviderClass();

        if (!$this->containsKey($configuredPictureProvider)) {
            throw new Exception($this->getTranslator()->trans('InvalidUserPictureProvider'));
        }

        return $this->get($configuredPictureProvider);
    }

    public function getActivePictureProviderClass(): string
    {
        return $this->activePictureProviderClass;
    }

    /**
     * @return string[]
     */
    public function getAvailablePictureProviderTypes(): array
    {
        return $this->getKeys();
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}
