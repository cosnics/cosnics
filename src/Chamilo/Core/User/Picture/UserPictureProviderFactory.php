<?php

namespace Chamilo\Core\User\Picture;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Platform\Translation;

/**
 * Factory to instantiate the user picture provider
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserPictureProviderFactory
{
    /**
     * The configuration
     *
     * @var Configuration
     */
    protected $configuration;

    /**
     * Constructor
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration = null)
    {
        if (is_null($configuration) || !$configuration instanceof Configuration)
        {
            $configuration = Configuration::getInstance();
        }

        $this->configuration = $configuration;
    }

    /**
     * Returns a list of available picture providers
     *
     * @return string[]
     */
    public function getAvailablePictureProviders()
    {
        $pictureProviders = array();

        $pictureProvidersPackages = $this->configuration->get_registrations_by_type(__NAMESPACE__ . '\\Provider');

        foreach ($pictureProvidersPackages as $package)
        {
            /** @var UserPictureProviderInterface|string $pictureProviderClass */
            $pictureProviderClass = $package['context'] . '\UserPictureProvider';

            if (class_exists($pictureProviderClass))
            {
                $pictureProviders[$pictureProviderClass] =
                    Translation::getInstance()->getTranslation('TypeName', array(), $package['context']);
            }
        }

        return $pictureProviders;
    }

    /**
     * Returns the active picture provider
     *
     * @return UserPictureProviderInterface
     *
     * @throws \Exception
     */
    public function getActivePictureProvider()
    {
        $pictureProvider = $this->configuration->get_setting(array('Chamilo\Core\User', 'user_picture_provider'));
        if (!class_exists($pictureProvider))
        {
            throw new \Exception(
                Translation::getInstance()->getTranslation('InvalidUserPictureProvider')
            );
        }

        return new $pictureProvider();
    }
}
