<?php
namespace Chamilo\Libraries\Utilities\ApplicationGenerator\ManagerGenerator;

use Chamilo\Libraries\Utilities\Utilities;

/**
 * Manager generator used to generate managers
 * 
 * @author Sven Vanpoucke
 */
class ManagerGenerator
{

    private $template;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->template = new MyTemplate();
        $this->template->set_rootdir(__DIR__);
    }

    public function generate_managers($location, $application_name, $classes, $author)
    {
        if (! is_dir($location))
            mkdir($location, 0777, true);

        $manager_file = fopen(
            $location (string) StringUtilities :: getInstance()->createString($application_name)->underscored() .
                 '_manager.class.php',
                'w+');
        
        if ($manager_file)
        {
            $this->template->set_filenames(array('manager' => 'manager.template'));
            
            $this->template->assign_vars(
                array(
                    'APPLICATION_NAME' => (string) StringUtilities::getInstance()->createString($application_name)->upperCamelize(), 
                    'L_APPLICATION_NAME' => (string) StringUtilities::getInstance()->createString($application_name)->underscored(), 
                    'AUTHOR' => $author, 
                    'NAMESPACE' => 'application\\' . $application_name));
            
            foreach ($classes as $class)
            {
                $class_lower = (string) StringUtilities::getInstance()->createString($class)->underscored();
                $class_upper = strtoupper($class_lower);
                $class2 = substr($class, - 1) == 'y' ? substr($class, 0, strlen($class) - 1) . 'ie' : $class;
                $class2 .= 's';
                $class2_lower = (string) StringUtilities::getInstance()->createString($class2)->underscored();
                $class2_upper = strtoupper($class2_lower);
                
                $this->template->assign_block_vars(
                    "OBJECTS", 
                    array(
                        'OBJECT_CLASS' => $class, 
                        'OBJECT_CLASSES' => $class2, 
                        'L_OBJECT_CLASS' => $class_lower, 
                        'U_OBJECT_CLASS' => $class_upper, 
                        'L_OBJECT_CLASSES' => $class2_lower, 
                        'U_OBJECT_CLASSES' => $class2_upper));
            }
            
            $string = trim($this->template->pparse_return('manager'));
            fwrite($manager_file, $string);
            fclose($manager_file);
        }
    }
}
