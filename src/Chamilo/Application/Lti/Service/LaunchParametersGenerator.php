<?php

namespace Chamilo\Application\Lti\Service;

use Chamilo\Application\Lti\Domain\LaunchParameters;
use Chamilo\Application\Lti\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\Redirect;

/**
 * Generates a LaunchParameters object with values based on the
 * current Chamilo configuration and user data using sensible defaults
 *
 * Class LaunchParametersGenerator
 * @package Chamilo\Application\Lti\Service
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class LaunchParametersGenerator
{
    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * @var \Chamilo\Libraries\File\PathBuilder
     */
    protected $pathBuilder;

    /**
     * @var \Chamilo\Configuration\Service\ConfigurationConsulter
     */
    protected $configurationConsulter;

    /**
     * LaunchParametersGenerator constructor.
     *
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     * @param \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
     */
    public function __construct(
        \Symfony\Component\Translation\Translator $translator, \Chamilo\Libraries\File\PathBuilder $pathBuilder,
        \Chamilo\Configuration\Service\ConfigurationConsulter $configurationConsulter
    )
    {
        $this->translator = $translator;
        $this->pathBuilder = $pathBuilder;
        $this->configurationConsulter = $configurationConsulter;
    }

    /**
     * Generates a LaunchParameters object with values based on the current Chamilo configuration and user data
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Application\Lti\Domain\LaunchParameters|null $launchParameters
     *
     * @return \Chamilo\Application\Lti\Domain\LaunchParameters|null
     */
    public function generateLaunchParametersForUser(User $user, LaunchParameters $launchParameters = null)
    {
        $presentationReturnUrl = new Redirect(
            [
                Manager::PARAM_CONTEXT => Manager::context(),
                Manager::PARAM_ACTION => Manager::ACTION_LAUNCH
            ]
        );

        if(!$launchParameters instanceof LaunchParameters)
        {
            $launchParameters = new LaunchParameters();
        }

        $launchParameters
            ->setLaunchPresentationDocumentTarget(LaunchParameters::DOCUMENT_TARGET_IFRAME)
            ->setLaunchPresentationLocale($this->translator->getLocale())
            ->setLaunchPresentationReturnUrl($presentationReturnUrl->getUrl())
            ->setLisPersonNameGiven($user->get_firstname())
            ->setLisPersonNameFamily($user->get_lastname())
            ->setLisPersonNameFull($user->get_fullname())
            ->setListPersonContactEmailPrimary($user->get_email())
            ->setToolConsumerInfoProductFamilyCode('cosnics')
            ->setToolConsumerInfoVersion('1.0')
            ->setToolConsumerInstanceContactEmail($this->configurationConsulter->getSetting(['Chamilo\Core\Admin', 'administrator_email']))
            ->setToolConsumerInstanceGuid($this->pathBuilder->getBasePath(true))
            ->setToolConsumerInstanceUrl($this->pathBuilder->getBasePath(true))
            ->setToolConsumerInstanceName($this->configurationConsulter->getSetting(['Chamilo\Core\Admin', 'site_name']))
            ->setUserId(md5($user->getId()));

        return $launchParameters;
    }
}