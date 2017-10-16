<?php
namespace Chamilo\Application\Calendar\Extension\Personal;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\Viewer\ActionSelector;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Personal
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Actions implements \Chamilo\Application\Calendar\ActionsInterface
{

    /**
     *
     * @see \Chamilo\Application\Calendar\ActionsInterface::getPrimary()
     */
    public function getPrimary(Application $application)
    {
        $parameters = array();
        $parameters[Application::PARAM_CONTEXT] = __NAMESPACE__;
        $parameters[Manager::PARAM_ACTION] = Manager::ACTION_CREATE;

        $actionSelector = new ActionSelector(
            $application,
            $application->getUser()->getId(),
            $this->getAllowedContentObjectTypes(),
            $parameters);

        $createButton = $actionSelector->getActionButton(Translation::get('AddEvent'), new FontAwesomeGlyph('plus'));
        $createButton->setClasses('btn-primary');

        return array($createButton);
    }

    /**
     *
     * @return string[]
     */
    public function getAllowedContentObjectTypes()
    {
        $registrations = Configuration::getInstance()->getIntegrationRegistrations(
            Manager::package(),
            \Chamilo\Core\Repository\Manager::package() . '\ContentObject');
        $types = array();

        foreach ($registrations as $registration)
        {
            $namespace = ClassnameUtilities::getInstance()->getNamespaceParent(
                $registration[Registration::PROPERTY_CONTEXT],
                6);
            $types[] = $namespace . '\Storage\DataClass\\' .
                 ClassnameUtilities::getInstance()->getPackageNameFromNamespace($namespace);
        }

        return $types;
    }

    /**
     *
     * @see \Chamilo\Application\Calendar\ActionsInterface::getAdditional()
     */
    public function getAdditional(Application $application)
    {
        return array();
    }
}