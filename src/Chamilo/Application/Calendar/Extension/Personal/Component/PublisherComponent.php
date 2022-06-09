<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Component;

use Chamilo\Application\Calendar\Extension\Personal\Form\PublicationForm;
use Chamilo\Application\Calendar\Extension\Personal\Manager;
use Chamilo\Application\Calendar\Extension\Personal\Publisher\PublicationHandler;
use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\Publication\Publisher\Interfaces\PublisherSupport;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
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
     * @var \Chamilo\Application\Calendar\Extension\Personal\Form\PublicationForm
     */
    protected $publicationForm;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $applicationConfiguration = new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this);

        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Repository\Publication\Publisher\Manager::context(), $applicationConfiguration
        )->run();
    }

    /**
     * @param \Chamilo\Libraries\Format\Structure\BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('personal_calendar_publisher');
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject[] $selectedContentObjects
     *
     * @return \Chamilo\Application\Calendar\Extension\Personal\Form\PublicationForm
     * @throws \Exception
     */
    public function getPublicationForm($selectedContentObjects = [])
    {
        if (!isset($this->publicationForm))
        {
            $this->publicationForm = new PublicationForm(
                $this->getUser(), $this->get_url(), $selectedContentObjects
            );
        }

        return $this->publicationForm;
    }

    /**
     * @return \Chamilo\Application\Calendar\Extension\Personal\Publisher\PublicationHandler
     */
    public function getPublicationHandler()
    {
        return new PublicationHandler($this->publicationForm, $this, $this->getPublicationService());
    }

    /**
     * @return string[]
     */
    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Core\Repository\Viewer\Manager::PARAM_ID;
        $additionalParameters[] = \Chamilo\Core\Repository\Viewer\Manager::PARAM_ACTION;

        return parent::getAdditionalParameters($additionalParameters);
    }

    /**
     *
     * @return string[]
     */
    public function get_allowed_content_object_types()
    {
        $registrations = Configuration::getInstance()->getIntegrationRegistrations(
            Manager::package(), \Chamilo\Core\Repository\Manager::package() . '\ContentObject'
        );
        $types = [];

        foreach ($registrations as $registration)
        {
            $namespace = ClassnameUtilities::getInstance()->getNamespaceParent(
                $registration[Registration::PROPERTY_CONTEXT], 6
            );
            $types[] = $namespace . '\Storage\DataClass\\' .
                ClassnameUtilities::getInstance()->getPackageNameFromNamespace($namespace);
        }

        return $types;
    }
}
