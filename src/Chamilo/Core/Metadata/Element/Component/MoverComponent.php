<?php
namespace Chamilo\Core\Metadata\Element\Component;

use Chamilo\Core\Metadata\Element\Manager;
use Chamilo\Core\Metadata\Element\Storage\DataManager;
use Chamilo\Core\Metadata\Storage\DataClass\Element;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Exception;

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
        if ($metadata_element_id = Request::get(Manager::PARAM_ELEMENT_ID))
        {
            $this->set_parameter(self::PARAM_ELEMENT_ID, $metadata_element_id);
            
            $metadata_element = DataManager::retrieve_by_id(Element::class, $metadata_element_id);
            if ($move = Request::get(Manager::PARAM_MOVE))
            {
                $this->set_parameter(self::PARAM_MOVE, $move);
                
                $metadata_element->move($move);
                
                $this->redirect(
                    '', 
                    false, 
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_BROWSE, 
                        \Chamilo\Core\Metadata\Schema\Manager::PARAM_SCHEMA_ID => $metadata_element->get_schema_id()), 
                    $this->getAdditionalParameters());
            }
            else
            {
                throw new Exception(Translation::get('MoveDirectionNotSelected'));
            }
        }
        else
        {
            throw new NoObjectSelectedException(Translation::get('Element'));
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
                    array(Manager::PARAM_ACTION => Manager::ACTION_BROWSE), 
                    $this->getAdditionalParameters()),
                Translation::get('BrowserComponent')));
    }
}