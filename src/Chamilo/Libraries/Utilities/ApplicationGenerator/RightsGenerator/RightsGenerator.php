<?php
namespace Chamilo\Libraries\Utilities\ApplicationGenerator\RightsGenerator;

use Chamilo\Libraries\Utilities\Utilities;

/**
 * Dataclass generator used to generate rights xml files
 *
 * @author Sven Vanpoucke
 */
class RightsGenerator
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
     * Generate a rights xml file with the given info
     *
     * @param string $location - The location of the class
     * @param string $application_name - The name of the application
     */
    public function generate_right_files($location, $application_name)
    {
        if (! is_dir($location))
            mkdir($location, 0777, true);

        $file = fopen(
            $location (string) StringUtilities :: getInstance()->createString($application_name)->underscored() .
                 '_locations.xml',
                'w+');

        if ($file)
        {
            $this->template->set_filenames(array('rights' => 'rights.template'));

            $this->template->assign_vars(array('APPLICATION_NAME' => $application_name));

            $string = trim($this->template->pparse_return('rights'));
            fwrite($file, $string);
            fclose($file);
        }
    }
}
