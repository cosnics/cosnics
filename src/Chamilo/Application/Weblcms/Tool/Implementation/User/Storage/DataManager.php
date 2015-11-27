<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Storage;

use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * This class represents the data manager for this package
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 * @package application.weblcms.tool.assignment
 */
class DataManager extends \Chamilo\Libraries\Storage\DataManager\DataManager
{
    const PREFIX = 'weblcms_';

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    public static function get_connector_toolbar_items($user_id)
    {
        $toolbar_items = array();
        
        $path = __DIR__ . '/../../connector/';
        $files = Filesystem :: get_directory_content($path, Filesystem :: LIST_FILES, false);
        $toolbar_items = array();
        foreach ($files as $file)
        {
            
            $file_class = split('.class.php', $file);
            require_once $path . $file;
            $class = __NAMESPACE__ . '\\' .
                 (string) StringUtilities :: getInstance()->createString($file_class[0])->upperCamelize();
            
            $connector = new $class();
            
            if ($connector->is_active())
            {
                $toolbar_items = array_merge($toolbar_items, $connector->get_toolbar_items($user_id));
            }
        }
        
        return $toolbar_items;
    }
}
