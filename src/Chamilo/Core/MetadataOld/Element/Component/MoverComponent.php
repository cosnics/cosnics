<?php
namespace Chamilo\Core\MetadataOld\Element\Component;

use Chamilo\Core\MetadataOld\Element\Manager;
use Chamilo\Core\MetadataOld\Element\Storage\DataClass\Element;
use Chamilo\Core\MetadataOld\Element\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 * Component to move metadata_element object
 * 
 * @author Sven Vanpoucke
 */
class MoverComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if ($metadata_element_id = Request :: get(Manager :: PARAM_ELEMENT_ID))
        {
            $metadata_element = DataManager :: retrieve_by_id(Element :: class_name(), $metadata_element_id);
            if ($move = Request :: get(Manager :: PARAM_MOVE))
            {
                $metadata_element->move($move);
                
                $this->redirect(
                    '', 
                    false, 
                    array(Manager :: PARAM_ACTION => Manager :: ACTION_BROWSE), 
                    $this->get_additional_parameters());
            }
            else
            {
                throw new \Exception(Translation :: get('MoveDirectionNotSelected'));
            }
        }
        else
        {
            throw new NoObjectSelectedException(Translation :: get('Element'));
        }
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
                    array(Manager :: PARAM_ACTION => Manager :: ACTION_BROWSE), 
                    $this->get_additional_parameters()), 
                Translation :: get('BrowserComponent')));
    }

    /**
     * @inheritedDoc
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_ELEMENT_ID, self :: PARAM_MOVE);
    }
}