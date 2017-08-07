<?php
namespace Chamilo\Libraries\Utilities\ApplicationGenerator\InstallGenerator;

use Chamilo\Libraries\Utilities\ApplicationGenerator\MyTemplate;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Dataclass generator used to generate install files
 * 
 * @author Sven Vanpoucke
 */
class InstallGenerator
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

    /**
     * Generate install files with the given info
     * 
     * @param string $location - The location of the class
     * @param string $application_name - The name of the application
     */
    public function generate_install_files($location, $application_name, $author)
    {
        if (! is_dir($location))
            mkdir($location, 0777, true);

        $file = fopen(
            $location . (string) StringUtilities :: getInstance()->createString($application_name)->underscored() .
                 '_installer.class.php',
                'w+');
        
        if ($file)
        {
            $this->template->set_filenames(array('install' => 'install.template'));
            
            $this->template->assign_vars(
                array(
                    'APPLICATION_NAME' => (string) StringUtilities::getInstance()->createString($application_name)->underscored(), 
                    'C_APPLICATION_NAME' => (string) StringUtilities::getInstance()->createString($application_name)->upperCamelize(), 
                    'AUTHOR' => $author, 
                    'NAMESPACE' => 'application\\' . $application_name));
            
            $string = trim($this->template->pparse_return('install'));
            fwrite($file, $string);
            fclose($file);
        }
    }
}
