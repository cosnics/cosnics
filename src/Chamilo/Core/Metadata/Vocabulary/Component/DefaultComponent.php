<?php
namespace Chamilo\Core\Metadata\Vocabulary\Component;

use Chamilo\Core\Metadata\Storage\DataClass\Vocabulary;
use Chamilo\Core\Metadata\Vocabulary\Manager;
use Chamilo\Core\Metadata\Vocabulary\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class DefaultComponent extends Manager
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
        
        $vocabulary_ids = Request::get(self::PARAM_VOCABULARY_ID);
        
        try
        {
            if (empty($vocabulary_ids))
            {
                throw new NoObjectSelectedException(Translation::get('Vocabulary'));
            }
            
            if (! is_array($vocabulary_ids))
            {
                $vocabulary_ids = array($vocabulary_ids);
            }
            
            foreach ($vocabulary_ids as $vocabulary_id)
            {
                $vocabulary = DataManager::retrieve_by_id(Vocabulary::class_name(), $vocabulary_id);
                
                if ($vocabulary->isDefault())
                {
                    $vocabulary->set_default_value(0);
                }
                else
                {
                    $vocabulary->set_default_value(1);
                }
                
                if (! $vocabulary->update())
                {
                    throw new \Exception(
                        Translation::get(
                            'ObjectNotUpdated', 
                            array('OBJECT' => Translation::get('VocabularyDefault')), 
                            Utilities::COMMON_LIBRARIES));
                }
            }
            
            $success = true;
            $message = Translation::get(
                'ObjectUpdated', 
                array('OBJECT' => Translation::get('VocabularyDefault')), 
                Utilities::COMMON_LIBRARIES);
        }
        catch (\Exception $ex)
        {
            $success = false;
            $message = $ex->getMessage();
        }
        
        $this->redirect(
            $message, 
            ! $success, 
            array(
                self::PARAM_ACTION => self::ACTION_BROWSE, 
                \Chamilo\Core\Metadata\Element\Manager::PARAM_ELEMENT_ID => $vocabulary->get_element_id(), 
                \Chamilo\Core\Metadata\Vocabulary\Manager::PARAM_USER_ID => $vocabulary->get_user_id()));
    }
}