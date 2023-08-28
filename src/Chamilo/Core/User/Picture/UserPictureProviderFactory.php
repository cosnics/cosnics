<?php
namespace Chamilo\Core\User\Picture;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Configuration\Service\Consulter\RegistrationConsulter;
use Exception;
use Symfony\Component\Translation\Translator;

/**
 * Factory to instantiate the user picture provider
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserPictureProviderFactory
{
    /**
     * @var \Chamilo\Core\User\Picture\UserPictureProviderInterface[]
     */
    protected array $availablePictureProviders = [];

    protected ConfigurationConsulter $configurationConsulter;

    protected RegistrationConsulter $registrationConsulter;

    protected Translator $translator;

    public function __construct(
        ConfigurationConsulter $configurationConsulter, RegistrationConsulter $registrationConsulter,
        Translator $translator
    )
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->registrationConsulter = $registrationConsulter;
        $this->translator = $translator;
    }

    public function addAvailablePictureProvider(UserPictureProviderInterface $userPictureProvider): void
    {
        $this->availablePictureProviders[get_class($userPictureProvider)] = $userPictureProvider;
    }

    /**
     * Returns the active picture provider
     *
     * @return UserPictureProviderInterface
     * @throws \Exception
     */
    public function getActivePictureProvider(): UserPictureProviderInterface
    {
        $pictureProvider =
            $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\User', 'user_picture_provider']);

        if (!class_exists($pictureProvider))
        {
            throw new Exception($this->getTranslator()->trans('InvalidUserPictureProvider'));
        }

        return $this->availablePictureProviders[$pictureProvider];
    }

    /**
     * @return string[]
     */
    public function getAvailablePictureProviderTypes(): array
    {
        return array_keys($this->availablePictureProviders);
    }

    /**
     * Returns a list of available picture providers
     *
     * @return \Chamilo\Core\User\Picture\UserPictureProviderInterface[]
     */
    public function getAvailablePictureProviders(): array
    {
        return $this->availablePictureProviders;
    }

    /**
     * @return \Chamilo\Configuration\Service\Consulter\ConfigurationConsulter
     */
    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    /**
     * @return \Chamilo\Configuration\Service\Consulter\RegistrationConsulter
     */
    public function getRegistrationConsulter(): RegistrationConsulter
    {
        return $this->registrationConsulter;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @param \Chamilo\Configuration\Service\Consulter\ConfigurationConsulter $configurationConsulter
     *
     * @return UserPictureProviderFactory
     */
    public function setConfigurationConsulter(
        ConfigurationConsulter $configurationConsulter
    ): UserPictureProviderFactory
    {
        $this->configurationConsulter = $configurationConsulter;

        return $this;
    }

    /**
     * @param \Chamilo\Configuration\Service\Consulter\RegistrationConsulter $registrationConsulter
     *
     * @return UserPictureProviderFactory
     */
    public function setRegistrationConsulter(RegistrationConsulter $registrationConsulter): UserPictureProviderFactory
    {
        $this->registrationConsulter = $registrationConsulter;

        return $this;
    }

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     *
     * @return UserPictureProviderFactory
     */
    public function setTranslator(Translator $translator): UserPictureProviderFactory
    {
        $this->translator = $translator;

        return $this;
    }
}
