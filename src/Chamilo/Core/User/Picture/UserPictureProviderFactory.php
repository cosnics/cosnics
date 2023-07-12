<?php
namespace Chamilo\Core\User\Picture;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Configuration\Service\Consulter\RegistrationConsulter;
use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use Exception;
use Symfony\Component\Translation\Translator;

/**
 * Factory to instantiate the user picture provider
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserPictureProviderFactory
{
    use DependencyInjectionContainerTrait;

    /**
     * @var \Chamilo\Configuration\Service\Consulter\ConfigurationConsulter
     */
    protected $configurationConsulter;

    /**
     * @var \Chamilo\Configuration\Service\Consulter\RegistrationConsulter
     */
    protected $registrationConsulter;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * @param \Chamilo\Configuration\Service\Consulter\ConfigurationConsulter $configurationConsulter
     * @param \Chamilo\Configuration\Service\Consulter\RegistrationConsulter $registrationConsulter
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(
        ConfigurationConsulter $configurationConsulter, RegistrationConsulter $registrationConsulter,
        Translator $translator
    )
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->registrationConsulter = $registrationConsulter;
        $this->translator = $translator;
    }

    /**
     * Returns the active picture provider
     *
     * @return UserPictureProviderInterface
     * @throws \Exception
     */
    public function getActivePictureProvider()
    {
        $pictureProvider =
            $this->getConfigurationConsulter()->getSetting(['Chamilo\Core\User', 'user_picture_provider']);

        if (!class_exists($pictureProvider))
        {
            throw new Exception($this->getTranslator()->trans('InvalidUserPictureProvider'));
        }

        return $this->getService($pictureProvider);
    }

    /**
     * Returns a list of available picture providers
     *
     * @return string[]
     */
    public function getAvailablePictureProviders()
    {
        $pictureProviders = [];

        $pictureProvidersPackages =
            $this->getRegistrationConsulter()->getRegistrationsByType(__NAMESPACE__ . '\\Provider');

        foreach ($pictureProvidersPackages as $package)
        {
            /** @var UserPictureProviderInterface|string $pictureProviderClass */
            $pictureProviderClass = $package['context'] . '\UserPictureProvider';

            if (class_exists($pictureProviderClass))
            {
                $pictureProviders[$pictureProviderClass] = $this->getTranslator()->trans(
                    'TypeName', [], $package['context']
                );
            }
        }

        return $pictureProviders;
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
