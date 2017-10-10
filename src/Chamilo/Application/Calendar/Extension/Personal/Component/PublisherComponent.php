<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Component;

use Chamilo\Application\Calendar\Extension\Personal\Form\PublicationForm;
use Chamilo\Application\Calendar\Extension\Personal\Manager;
use Chamilo\Application\Calendar\Extension\Personal\Publisher\PublicationHandler;
use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\Publication\Publisher\Interfaces\PublicationHandlerInterface;
use Chamilo\Core\Repository\Publication\Publisher\Interfaces\PublisherSupport;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 *
 * @package application\calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublisherComponent extends Manager implements PublisherSupport, DelegateComponent
{

    /**
     * The publication form
     *
     * @var PublicationForm
     */
    protected $publicationForm;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $applicationConfiguration = new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this);
        $applicationConfiguration->set(\Chamilo\Core\Repository\Viewer\Manager::SETTING_TABS_DISABLED, true);

        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Repository\Publication\Publisher\Manager::context(),
            $applicationConfiguration)->run();
    }

    /**
     *
     * @return string[]
     */
    public function get_allowed_content_object_types()
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
     * Returns the publication form
     *
     * @param ContentObject[] $selectedContentObjects
     *
     * @return FormValidator
     */
    public function getPublicationForm($selectedContentObjects = array())
    {
        $ids = array();
        foreach ($selectedContentObjects as $selectedContentObject)
        {
            $ids[] = $selectedContentObject->getId();
        }

        $this->publicationForm = new PublicationForm(
            PublicationForm::TYPE_MULTI,
            $ids,
            $this->getUser(),
            $this->get_url(),
            $selectedContentObjects);

        return $this->publicationForm;
    }

    /**
     * Returns the publication handler
     *
     * @return PublicationHandlerInterface
     */
    public function getPublicationHandler()
    {
        return new PublicationHandler($this->publicationForm, $this);
    }

    /**
     *
     * @see \libraries\architecture\application\Application::add_additional_breadcrumbs()
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('personal_calendar_publisher');
    }

    public function get_additional_parameters()
    {
        return array(
            \Chamilo\Core\Repository\Viewer\Manager::PARAM_ID,
            \Chamilo\Core\Repository\Viewer\Manager::PARAM_ACTION);
    }
}
