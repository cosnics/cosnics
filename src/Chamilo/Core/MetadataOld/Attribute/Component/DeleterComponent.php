<?php
namespace Chamilo\Core\MetadataOld\Attribute\Component;

use Chamilo\Core\MetadataOld\Attribute\Manager;
use Chamilo\Core\MetadataOld\Attribute\Storage\DataClass\Attribute;
use Chamilo\Core\MetadataOld\Attribute\Storage\DataManager;
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
        
        $attribute_ids = Request :: get(self :: PARAM_ATTRIBUTE_ID);
        
        try
        {
            if (empty($attribute_ids))
            {
                throw new NoObjectSelectedException(Translation :: get('Element'));
            }
            
            if (! is_array($attribute_ids))
            {
                $attribute_ids = array($attribute_ids);
            }
            
            foreach ($attribute_ids as $attribute_id)
            {
                $attribute = DataManager :: retrieve_by_id(Attribute :: class_name(), $attribute_id);
                
                if (! $attribute->delete())
                {
                    throw new \Exception(
                        Translation :: get(
                            'ObjectNotDeleted', 
                            array('OBJECT' => Translation :: get('Attribute')), 
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
                    array(self :: PARAM_ATTRIBUTE_ID)), 
                Translation :: get('BrowserComponent')));
    }

    /**
     * @inheritedDoc
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_ATTRIBUTE_ID);
    }
}