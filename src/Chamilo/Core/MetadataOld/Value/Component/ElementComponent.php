<?php
namespace Chamilo\Core\MetadataOld\Value\Component;

use Chamilo\Core\MetadataOld\Value\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;

class ElementComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        $factory = new ApplicationFactory(
            \Chamilo\Core\MetadataOld\Value\Element\Manager :: context(),
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        return $factory->run();
    }

    /**
     * Adds additional breadcrumbs
     *
     * @param \libraries\format\BreadcrumbTrail $breadcrumb_trail
     * @param BreadcrumbTrail $breadcrumb_trail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumb_trail)
    {
        $breadcrumb_trail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        \Chamilo\Core\MetadataOld\Manager :: PARAM_ACTION => \Chamilo\Core\MetadataOld\Manager :: ACTION_ELEMENT),
                    $this->get_additional_parameters()),
                Translation :: get('BrowserComponent', null, 'Chamilo\Core\MetadataOld\Element')));
    }

    /**
     * Returns the additional parameters
     *
     * @return array
     */
    public function get_additional_parameters()
    {
        return array(\Chamilo\Core\MetadataOld\Element\Manager :: PARAM_ELEMENT_ID);
    }
}
