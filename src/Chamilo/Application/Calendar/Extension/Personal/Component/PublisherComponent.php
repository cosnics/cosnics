<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Component;

use Chamilo\Application\Calendar\Extension\Personal\Form\PublicationForm;
use Chamilo\Application\Calendar\Extension\Personal\Manager;
use Chamilo\Application\Calendar\Extension\Personal\Publisher\PublicationHandler;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\Publication\Publisher\Interfaces\PublisherSupport;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;

/**
 * @package Chamilo\Application\Calendar\Extension\Personal\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublisherComponent extends Manager implements PublisherSupport, BreadcrumbLessComponentInterface
{

    protected PublicationForm $publicationForm;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function run()
    {
        $applicationConfiguration = new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this);

        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Repository\Publication\Publisher\Manager::CONTEXT, $applicationConfiguration
        )->run();
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
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject[] $selectedContentObjects
     *
     * @return \Chamilo\Application\Calendar\Extension\Personal\Form\PublicationForm
     * @throws \Exception
     */
    public function getPublicationForm($selectedContentObjects = []): PublicationForm
    {
        if (!isset($this->publicationForm))
        {
            $this->publicationForm = new PublicationForm(
                $this->getUser(), $this->get_url(), $selectedContentObjects
            );
        }

        return $this->publicationForm;
    }

    public function getPublicationHandler(): PublicationHandler
    {
        return new PublicationHandler($this->publicationForm, $this, $this->getPublicationService());
    }

    /**
     * @return string[]
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function get_allowed_content_object_types()
    {
        $registrations = $this->getRegistrationConsulter()->getIntegrationRegistrations(
            Manager::CONTEXT, \Chamilo\Core\Repository\Manager::CONTEXT . '\ContentObject'
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
