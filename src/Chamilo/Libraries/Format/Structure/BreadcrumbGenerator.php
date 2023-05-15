<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Architecture\Interfaces\NoContextComponent;
use Chamilo\Libraries\Translation\Translation;

/**
 * Standard breadcrumb generator.
 * Generates a breadcrumb based on the package and component name. Includes the
 * possibility to add additional breadcrumbs between the package breadcrumb and the component breadcrumb
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class BreadcrumbGenerator implements BreadcrumbGeneratorInterface
{

    protected Application $application;

    protected BreadcrumbTrail $breadcrumbTrail;

    public function __construct(Application $component, BreadcrumbTrail $breadcrumbTrail)
    {
        $this->breadcrumbTrail = $breadcrumbTrail;
        $this->application = $component;
    }

    /**
     * @throws \ReflectionException
     */
    public function generateBreadcrumbs()
    {
        $application = $this->getApplication();

        if (!$application instanceof NoContextComponent && !$application->get_application() instanceof Application)
        {
            $this->generatePackageBreadcrumb();
        }

        $application->add_additional_breadcrumbs($this->getBreadcrumbTrail());

        if (!$application instanceof DelegateComponent)
        {
            $this->generateComponentBreadcrumb();
        }
    }

    /**
     * @throws \ReflectionException
     */
    protected function generateComponentBreadcrumb()
    {
        $application = $this->getApplication();
        $variable = ClassnameUtilities::getInstance()->getClassNameFromNamespace(get_class($application));

        $this->getBreadcrumbTrail()->add(
            new Breadcrumb(
                $application->get_url(), Translation::get($variable, null, $application::CONTEXT)
            )
        );
    }

    /**
     * @throws \ReflectionException
     */
    protected function generatePackageBreadcrumb()
    {
        $application = $this->getApplication();

        $filter_parameters = $application->getAdditionalParameters();
        $filter_parameters[] = $application::PARAM_ACTION;

        $this->getBreadcrumbTrail()->add(
            new Breadcrumb(
                $application->get_url([], $filter_parameters), Translation::get('TypeName', null, $application::CONTEXT)
            )
        );
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function getBreadcrumbTrail(): BreadcrumbTrail
    {
        return $this->breadcrumbTrail;
    }

    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    public function setBreadcrumbTrail(BreadcrumbTrail $breadcrumbTrail)
    {
        $this->breadcrumbTrail = $breadcrumbTrail;
    }
}