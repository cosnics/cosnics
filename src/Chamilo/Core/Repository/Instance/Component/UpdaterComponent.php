<?php
namespace Chamilo\Core\Repository\Instance\Component;

use Chamilo\Core\Repository\Instance\Form\InstanceForm;
use Chamilo\Core\Repository\Instance\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\Repository\Instance\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class UpdaterComponent extends Manager
{

    private $external_instance;

    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }
        
        $instance_id = Request::get(self::PARAM_INSTANCE_ID);
        
        if (isset($instance_id))
        {
            $this->external_instance = DataManager::retrieve_by_id(Instance::class, $instance_id);
            
            $form = new InstanceForm($this, $this->external_instance);
            
            if ($form->validate())
            {
                $success = $form->update_external_instance();
                $this->redirect(
                    Translation::get(
                        $success ? 'ObjectUpdated' : 'ObjectNotUpdated', 
                        array('OBJECT' => Translation::get('ExternalInstance')), 
                        Utilities::COMMON_LIBRARIES), !$success,
                    array(self::PARAM_ACTION => self::ACTION_BROWSE));
            }
            else
            {
                $html = [];
                
                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();
                
                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            return $this->display_error_page(
                Translation::get(
                    'NoObjectSelected', 
                    array('OBJECT' => Translation::get('ExternalInstance')), 
                    Utilities::COMMON_LIBRARIES));
        }
    }

    public function get_implementation()
    {
        return $this->external_instance->get_implementation();
    }
}
