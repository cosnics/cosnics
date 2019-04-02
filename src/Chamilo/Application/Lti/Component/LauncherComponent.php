<?php

namespace Chamilo\Application\Lti\Component;

use Chamilo\Application\Lti\Domain\Application;
use Chamilo\Application\Lti\Domain\Role\ContextRole;
use Chamilo\Application\Lti\Manager;
use Chamilo\Application\Lti\Service\LaunchGenerator;
use Chamilo\Application\Lti\Service\LaunchParametersGenerator;

/**
 * Class LauncherComponent
 *
 * @package Chamilo\Application\Lti
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class LauncherComponent extends Manager
{
    /**
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    function run()
    {
        $launchParametersGenerator = new LaunchParametersGenerator($this->getTranslator(), $this->getPathBuilder(), $this->getConfigurationConsulter());

        $launchParameters = $launchParametersGenerator->generateLaunchParametersForUser($this->getUser());
        $launchParameters->setContextId('9d5d6098a0763716622ebb48921d548713d1bae8')
            ->setContextLabel('ALGCUR001')
            ->setContextTitle('Demo Cursus')
            ->setResourceLinkId('9d5d6098a0763716622ebb48921d548713d1bae8')
            ->setResourceLinkTitle('BuddyCheck')
            ->addRole(new ContextRole(ContextRole::ROLE_INSTRUCTOR))
            ->getLearningInformationServicesParameters()
                ->setResultSourcedId(6);

        $ltiApplication = new Application(
            'http://dev.hogent.be/extra/lti_provider/src/connect.php', 'thisismychamilokey',
            '7Kts2OivnUnTZ6iCwdKgJSGJzYUqo3aD'
        );

        $launchGenerator = new LaunchGenerator($this->getTwig());

        $html = [];
        $html[] = $this->render_header();
        $html[] = $launchGenerator->generateLaunchHtml($ltiApplication, $launchParameters);
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }
}