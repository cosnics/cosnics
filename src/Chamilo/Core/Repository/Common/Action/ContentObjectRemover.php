<?php
namespace Chamilo\Core\Repository\Common\Action;

use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;

/**
 * Extension of the generic remover for content objects
 * 
 * @author Hans De Bisschop
 */
abstract class ContentObjectRemover extends \Chamilo\Configuration\Package\Action\Remover
{

    /**
     * Constructor
     * 
     * @param $values multitype:mixed
     */
    public function __construct($values)
    {
        parent :: __construct($values, DataManager :: get_instance());
    }

    /**
     * Perform additional installation steps
     * 
     * @return boolean
     */
    public function extra()
    {
        $context = self :: context();
        $class = $context . '\\' . ClassnameUtilities :: getInstance()->getPackageNameFromNamespace($context, true);
        
        $content_objects = DataManager :: retrieve_content_objects($class :: class_name());
        
        while ($content_object = $content_objects->next_result())
        {
            
            $content_object_versions = $content_object->get_content_object_versions();
            
            // Unlink the object
            foreach ($content_object_versions as $content_object_version)
            {
                $content_object_version->delete_links();
            }
            
            // Remove the object
            foreach ($content_object_versions as $content_object_version)
            {
                $content_object_version->delete();
            }
        }
        
        return true;
    }
}
