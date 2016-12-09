<?php
namespace Chamilo\Libraries\Utilities\ApplicationGenerator\PackageInfoGenerator;

use Chamilo\Libraries\Utilities\String\Text;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Dataclass generator used to generate autoloader files
 * 
 * @author Sven Vanpoucke
 */
class PackageInfoGenerator
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
    public function generate_package_info($location, $application_name, $author)
    {
        if (! is_dir($location))
            mkdir($location, 0777, true);
        
        $file = fopen($location . 'package/package.info', 'w+');
        
        if ($file)
        {
            $this->template->set_filenames(array('package_info' => 'package_info.template'));
            $this->template->assign_vars(
                array(
                    'L_APPLICATION_NAME' => $application_name, 
                    'AUTHOR' => $author, 
                    'C_APPLICATION_NAME' => (string) StringUtilities::getInstance()->createString($application_name)->upperCamelize(), 
                    'APPLICATION_NAME_FIRST_LETTER' => Text::char_at($application_name, 0)));
            
            $string = trim($this->template->pparse_return('package_info'));
            fwrite($file, $string);
            fclose($file);
        }
    }
}
