<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\Configuration;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataManager;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ConfigurationDeleterComponent extends Manager
{

    /**
     * Executes this component
     */
    public function run()
    {
        if ($this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()))
        {
            
            $config_ids = Request::get(self::PARAM_CONFIGURATION_ID);
            
            if (! is_array($config_ids))
            {
                $config_ids = array($config_ids);
            }
            
            if (count($config_ids) == 0)
            {
                $parameters = array(
                    self::PARAM_ACTION => self::ACTION_QUESTION_MANAGER, 
                    self::PARAM_STEP => $this->get_current_step());
                
                $this->redirect(
                    Translation::getInstance()->getTranslation(
                        'NoObjectsToDelete', 
                        array('OBJECTS' => Translation::getInstance()->getTranslation('Configuration')), 
                        Utilities::COMMON_LIBRARIES), 
                    true, 
                    $parameters);
            }
            
            $failures = 0;
            
            foreach ($config_ids as $config_id)
            {
                $configuration = DataManager::retrieve_by_id(Configuration::class_name(), $config_id);
                $success = $configuration->delete();
                
                if ($success)
                {
                    $content = Translation::getInstance()->getTranslation('configurationDeleted') . ' : ' .
                         $configuration->getName();
                    
                    Event::trigger(
                        'Activity', 
                        \Chamilo\Core\Repository\Manager::context(), 
                        array(
                            Activity::PROPERTY_TYPE => Activity::ACTIVITY_UPDATE_ITEM, 
                            Activity::PROPERTY_USER_ID => $this->get_user_id(), 
                            Activity::PROPERTY_DATE => time(), 
                            Activity::PROPERTY_CONTENT_OBJECT_ID => $this->get_current_node()->get_complex_content_object_item()->get_ref(), 
                            Activity::PROPERTY_CONTENT => $content));
                }
                else
                {
                    $failures ++;
                }
            }
            
            $this->redirect(
                Translation::getInstance()->getTranslation(
                    $failures > 0 ? 'ObjectsNotDeleted' : 'ObjectsDeleted', 
                    array('OBJECTS' => Translation::getInstance()->getTranslation('Configuration')), 
                    Utilities::COMMON_LIBRARIES), 
                $failures > 0, 
                array(
                    self::PARAM_ACTION => self::ACTION_QUESTION_MANAGER, 
                    self::PARAM_STEP => $this->get_current_step()));
        }
        else
        {
            throw new NotAllowedException();
        }
    }
}
