<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Property\Component;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Property\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Controller to delete a ContentObjectPropertyRelMetadataElement
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
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
        
        try
        {
            $content_object_rel_metadata_element_ids = $this->get_content_object_property_rel_metadata_element_id_from_request();
            
            if (! is_array($content_object_rel_metadata_element_ids))
            {
                $content_object_rel_metadata_element_ids = array($content_object_rel_metadata_element_ids);
            }
            
            foreach ($content_object_rel_metadata_element_ids as $content_object_rel_metadata_element_id)
            {
                $content_object_rel_metadata_element = $this->get_content_object_property_rel_metadata_element_by_id(
                    $content_object_rel_metadata_element_id);
                
                if (! $content_object_rel_metadata_element->delete())
                {
                    throw new \Exception(
                        Translation :: get(
                            'ObjectNotDeleted', 
                            array('OBJECT' => Translation :: get('ContentObjectPropertyRelMetadataElement')), 
                            Utilities :: COMMON_LIBRARIES));
                }
            }
            
            $success = true;
            $message = Translation :: get(
                'ObjectDeleted', 
                array('OBJECT' => Translation :: get('ContentObjectPropertyRelMetadataElement')), 
                Utilities :: COMMON_LIBRARIES);
        }
        catch (\Exception $ex)
        {
            $success = false;
            $message = $ex->getMessage();
        }
        
        $this->redirect(
            $message, 
            ! $success, 
            array(self :: PARAM_ACTION => self :: ACTION_BROWSE), 
            $this->get_additional_parameters());
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
        return array(self :: PARAM_CONTENT_OBJECT_PROPERTY_REL_METADATA_ELEMENT_ID);
    }
}