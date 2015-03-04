<?php
namespace Chamilo\Core\Metadata\Value\Element\Component;

use Chamilo\Core\Metadata\Value\Element\Manager;
use Chamilo\Core\Metadata\Value\Element\Storage\DataClass\DefaultElementValue;
use Chamilo\Core\Metadata\Value\Element\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

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
        
        $default_element_value_ids = Request :: get(self :: PARAM_ELEMENT_VALUE_ID);
        
        try
        {
            if (empty($default_element_value_ids))
            {
                throw new NoObjectSelectedException(Translation :: get('DefaultElementValue'));
            }
            
            if (! is_array($default_element_value_ids))
            {
                $default_element_value_ids = array($default_element_value_ids);
            }
            
            foreach ($default_element_value_ids as $default_element_value_id)
            {
                $default_element_value = DataManager :: retrieve_by_id(
                    DefaultElementValue :: class_name(), 
                    $default_element_value_id);
                
                if (! $default_element_value->delete())
                {
                    throw new \Exception(
                        Translation :: get(
                            'ObjectNotDeleted', 
                            array('OBJECT' => Translation :: get('DefaultElementValue')), 
                            Utilities :: COMMON_LIBRARIES));
                }
            }
            
            $success = true;
            $message = Translation :: get(
                'ObjectDeleted', 
                array('OBJECT' => Translation :: get('Attribute')), 
                Utilities :: COMMON_LIBRARIES);
        }
        catch (\Exception $ex)
        {
            $success = false;
            $message = $ex->getMessage();
        }
        
        $this->redirect($message, ! $success, array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
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
     * Returns the additional parameters
     * 
     * @return array
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_ELEMENT_VALUE_ID);
    }
}