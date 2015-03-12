<?php
namespace Chamilo\Core\Metadata\Value\Component;

use Chamilo\Core\Metadata\Value\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;

class ElementComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        $factory = new ApplicationFactory(
            $this->getRequest(),
            \Chamilo\Core\Metadata\Value\Element\Manager :: context(),
            $this->get_user(),
            $this);
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
                        \Chamilo\Core\Metadata\Manager :: PARAM_ACTION => \Chamilo\Core\Metadata\Manager :: ACTION_ELEMENT),
                    $this->get_additional_parameters()),
                Translation :: get('BrowserComponent', null, 'core\metadata\element')));
    }

    /**
     * Returns the additional parameters
     *
     * @return array
     */
    public function get_additional_parameters()
    {
        return array(\Chamilo\Core\Metadata\Element\Manager :: PARAM_ELEMENT_ID);
    }
}
