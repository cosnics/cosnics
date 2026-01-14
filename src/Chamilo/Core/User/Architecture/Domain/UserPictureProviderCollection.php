<?php
namespace Chamilo\Core\User\Architecture\Domain;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Architecture\Domain\Collection
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserPictureProviderCollection extends ArrayCollection
{
    protected ConfigurationConsulter $configurationConsulter;

    protected Translator $translator;

    public function __construct(ConfigurationConsulter $configurationConsulter, Translator $translator)
    {
        parent::__construct();

        $this->configurationConsulter = $configurationConsulter;
        $this->translator = $translator;
    }

    public function addAvailablePictureProvider(UserPictureProviderInterface $userPictureProvider): void
    {
        $this->set(get_class($userPictureProvider), $userPictureProvider);
    }

    /**
     * @return UserPictureProviderInterface
     * @throws \Exception
     */
    public function getActivePictureProvider(): UserPictureProviderInterface
    {
        $configuredPictureProvider =
            $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\User', 'user_picture_provider']);

        if (!$this->containsKey($configuredPictureProvider))
        {
            throw new Exception($this->getTranslator()->trans('InvalidUserPictureProvider'));
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

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}
