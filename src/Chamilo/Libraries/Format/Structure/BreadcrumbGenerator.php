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
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class BreadcrumbGenerator implements BreadcrumbGeneratorInterface
{

    /**
     * The application component
     *
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    protected $component;

    /**
     * The breadcrumb trail instance
     *
     * @var \Chamilo\Libraries\Format\Structure\BreadcrumbTrail
     */
    protected $breadcrumb_trail;

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $component
     * @param \Chamilo\Libraries\Format\Structure\BreadcrumbTrail $breadcrumbTrail
     */
    public function __construct(Application $component, BreadcrumbTrail $breadcrumbTrail)
    {
        $this->breadcrumb_trail = $breadcrumbTrail;
        $this->component = $component;
    }

    /**
     * Automatically generates the breadcrumbs based on the given component
     */
    public function generate_breadcrumbs()
    {
        $component = $this->component;
        $context = $component->package();

        if (!$component instanceof NoContextComponent && !$component->get_application() instanceof Application)
        {
            $this->generate_package_breadcrumb();
        }

        $component->add_additional_breadcrumbs($this->breadcrumb_trail);

        if (!$component instanceof DelegateComponent)
        {
            $this->breadcrumb_trail->add_help(
                $context, ClassnameUtilities::getInstance()->getClassnameFromObject($component, true)
            );

            $this->generate_component_breadcrumb();
        }
    }

    /**
     * Generates the breadcrumb for the component name
     */
    protected function generate_component_breadcrumb()
    {
        $variable = ClassnameUtilities::getInstance()->getClassNameFromNamespace(get_class($this->component));

        $this->breadcrumb_trail->add(
            new Breadcrumb($this->component->get_url(), Translation::get($variable, null, $this->component->package()))
        );
    }

    /**
     * Generates the breadcrumb for the package name
     */
    protected function generate_package_breadcrumb()
    {
        $component = $this->component;
        $context = $component->package();

        $filter_parameters = $component->get_additional_parameters();
        $filter_parameters[] = $component::PARAM_ACTION;

        $this->breadcrumb_trail->add(
            new Breadcrumb(
                $component->get_url([], $filter_parameters), Translation::get('TypeName', null, $context)
            )
        );
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\BreadcrumbTrail
     */
    public function get_breadcrumb_trail()
    {
        return $this->breadcrumb_trail;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\BreadcrumbTrail $breadcrumbTrail
     */
    public function set_breadcrumb_trail($breadcrumbTrail)
    {
        $this->breadcrumb_trail = $breadcrumbTrail;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function get_component()
    {
        return $this->component;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $component
     */
    public function set_component($component)
    {
        $this->component = $component;
    }
}