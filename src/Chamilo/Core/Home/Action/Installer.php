<?php
namespace Chamilo\Core\Home\Action;

use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Storage\DataClass\BlockRegistration;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Platform\Translation;

/**
 * Extension of the generic installer for home blocks
 * 
 * @author Hans De Bisschop
 */
abstract class Installer extends \Chamilo\Configuration\Package\Action\Installer
{

    /**
     * Perform additional installation steps
     * 
     * @return boolean
     */
    public function extra()
    {
        if (! $this->register_home())
        {
            return $this->failed(Translation :: get('HomeFailed', null, Manager :: APPLICATION_NAME));
        }
        
        return true;
    }

    /**
     *
     * @return boolean
     */
    public function register_home()
    {
        $type_path = static :: get_path() . 'Type';
        
        if (is_dir($type_path))
        {
            $files = Filesystem :: get_directory_content($type_path, Filesystem :: LIST_FILES);
            
            if (count($files) > 0)
            {
                foreach ($files as $file)
                {
                    $file_path_info = pathinfo($file);
                    
                    if ($file_path_info['extension'] === 'php')
                    {
                        $file_info = pathinfo($file_path_info['filename']);
                        $block_name = $file_info['filename'];
                        
                        $home_block_registration = new BlockRegistration();
                        $home_block_registration->set_context(static :: package());
                        $home_block_registration->set_block($block_name);
                        
                        if ($home_block_registration->create())
                        {
                            $this->add_message(
                                self :: TYPE_NORMAL, 
                                Translation :: get('RegisteredBlock') . ': <em>' . $block_name . '</em>');
                        }
                        else
                        {
                            $this->add_message(
                                self :: TYPE_ERROR, 
                                Translation :: get('BlockRegistrationFailed') . ': <em>' . $block_name . '</em>');
                            return false;
                        }
                    }
                }
                
                $this->add_message(self :: TYPE_NORMAL, Translation :: get('BlocksAdded'));
            }
        }
        
        return true;
    }
}
