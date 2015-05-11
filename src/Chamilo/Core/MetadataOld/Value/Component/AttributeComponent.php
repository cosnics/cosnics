<?php
namespace Chamilo\Core\MetadataOld\Value\Component;

use Chamilo\Core\MetadataOld\Value\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;

class AttributeComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        \Chamilo\Libraries\Architecture\Application\Application :: launch(
            \Chamilo\Core\MetadataOld\Value\Attribute\Manager :: context(),
            $this->get_user(),
            $this);
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
                        \Chamilo\Core\MetadataOld\Manager :: PARAM_ACTION => \Chamilo\Core\MetadataOld\Manager :: ACTION_ATTRIBUTE),
                    $this->get_additional_parameters()),
                Translation :: get('BrowserComponent', null, 'Chamilo\Core\MetadataOld\Attribute')));
    }

    /**
     * Returns the additional parameters
     *
     * @return array
     */
    public function get_additional_parameters()
    {
        return array(\Chamilo\Core\MetadataOld\Attribute\Manager :: PARAM_ATTRIBUTE_ID);
    }
}
