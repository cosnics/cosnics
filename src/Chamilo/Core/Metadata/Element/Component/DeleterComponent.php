<?php
namespace Chamilo\Core\Metadata\Element\Component;

use Chamilo\Core\Metadata\Element\Manager;
use Chamilo\Core\Metadata\Element\Storage\DataManager;
use Chamilo\Core\Metadata\Storage\DataClass\Element;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * Controller to delete the schema
 */
class DeleterComponent extends Manager
{

    /**
     * Executes this controller
     */
    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }
        
        $element_ids = $this->getRequest()->get(self::PARAM_ELEMENT_ID);
        $this->set_parameter(self::PARAM_ELEMENT_ID, $element_ids);
        
        try
        {
            if (empty($element_ids))
            {
                throw new NoObjectSelectedException(Translation::get('Element'));
            }
            
            if (! is_array($element_ids))
            {
                $element_ids = array($element_ids);
            }
            
            foreach ($element_ids as $element_id)
            {
                $element = DataManager::retrieve_by_id(Element::class, $element_id);
                
                if (! $element->delete())
                {
                    throw new Exception(
                        Translation::get(
                            'ObjectNotDeleted', 
                            array('OBJECT' => Translation::get('Element')), 
                            StringUtilities::LIBRARIES));
                }
            }
            
            $success = true;
            $message = Translation::get(
                'ObjectDeleted', 
                array('OBJECT' => Translation::get('Element')), 
                StringUtilities::LIBRARIES);
        }
        catch (Exception $ex)
        {
            $success = false;
            $message = $ex->getMessage();
        }
        
        $this->redirect(
            $message, 
            ! $success, 
            array(
                self::PARAM_ACTION => self::ACTION_BROWSE, 
                \Chamilo\Core\Metadata\Schema\Manager::PARAM_SCHEMA_ID => $element->get_schema_id()));
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
                $this->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_BROWSE), array(self::PARAM_ELEMENT_ID)), 
                Translation::get('BrowserComponent')));
    }
}