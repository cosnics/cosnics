<?php
namespace Chamilo\Core\MetadataOld\ControlledVocabulary\Component;

use Chamilo\Core\MetadataOld\ControlledVocabulary\Manager;
use Chamilo\Core\MetadataOld\ControlledVocabulary\Storage\DataClass\ControlledVocabulary;
use Chamilo\Core\MetadataOld\ControlledVocabulary\Storage\DataManager;
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
        
        $controlled_vocabulary_ids = Request :: get(self :: PARAM_CONTROLLED_VOCABULARY_ID);
        
        try
        {
            if (empty($controlled_vocabulary_ids))
            {
                throw new NoObjectSelectedException(Translation :: get('ControlledVocabulary'));
            }
            
            if (! is_array($controlled_vocabulary_ids))
            {
                $controlled_vocabulary_ids = array($controlled_vocabulary_ids);
            }
            
            foreach ($controlled_vocabulary_ids as $controlled_vocabulary_id)
            {
                $controlled_vocabulary = DataManager :: retrieve_by_id(
                    ControlledVocabulary :: class_name(), 
                    $controlled_vocabulary_id);
                
                if (! $controlled_vocabulary->delete())
                {
                    throw new \Exception(
                        Translation :: get(
                            'ObjectNotDeleted', 
                            array('OBJECT' => Translation :: get('ControlledVocabulary')), 
                            Utilities :: COMMON_LIBRARIES));
                }
            }
            
            $success = true;
            $message = Translation :: get(
                'ObjectDeleted', 
                array('OBJECT' => Translation :: get('ControlledVocabulary')), 
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
        return array(self :: PARAM_CONTROLLED_VOCABULARY_ID);
    }
}