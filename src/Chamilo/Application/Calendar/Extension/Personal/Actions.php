<?php
namespace Chamilo\Application\Calendar\Extension\Personal;

use Chamilo\Application\Calendar\ActionsInterface;
use Chamilo\Configuration\Service\Consulter\RegistrationConsulter;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\Viewer\ActionSelector;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Calendar\Extension\Personal
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Actions implements ActionsInterface
{
    protected ClassnameUtilities $classnameUtilities;

    protected RegistrationConsulter $registrationConsulter;

    protected Translator $translator;

    public function __construct(
        RegistrationConsulter $registrationConsulter, Translator $translator, ClassnameUtilities $classnameUtilities
    )
    {
        $this->registrationConsulter = $registrationConsulter;
        $this->translator = $translator;
        $this->classnameUtilities = $classnameUtilities;
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[]
     */
    public function getAdditional(Application $application): array
    {
        return [];
    }

    /**
     * @return string[]
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function getAllowedContentObjectTypes(): array
    {
        $classnameUtilities = $this->getClassnameUtilities();

        $registrations = $this->getRegistrationConsulter()->getIntegrationRegistrations(
            Manager::CONTEXT, \Chamilo\Core\Repository\Manager::CONTEXT . '\ContentObject'
        );
        $types = [];

        foreach ($registrations as $registration)
        {
            $namespace = $classnameUtilities->getNamespaceParent(
                $registration[Registration::PROPERTY_CONTEXT], 6
            );

            $types[] =
                $namespace . '\Storage\DataClass\\' . $classnameUtilities->getPackageNameFromNamespace($namespace);
        }

        return $types;
    }

    public function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->classnameUtilities;
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[]
     * @throws \Exception
     */
    public function getPrimary(Application $application): array
    {
        $parameters = [];
        $parameters[Application::PARAM_CONTEXT] = Manager::CONTEXT;
        $parameters[Manager::PARAM_ACTION] = Manager::ACTION_CREATE;

        $actionSelector = new ActionSelector(
            $application, $application->getUser()->getId(), $this->getAllowedContentObjectTypes(), $parameters
        );

        $createButton = $actionSelector->getActionButton(
            $this->getTranslator()->trans('AddEvent', [], Manager::CONTEXT), new FontAwesomeGlyph('plus')
        );
        $createButton->setClasses(['btn-primary']);

        return [$createButton];
    }

    public function getRegistrationConsulter(): RegistrationConsulter
    {
        return $this->registrationConsulter;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}