<?php
namespace Chamilo\Libraries\Utilities\ApplicationGenerator\SortableTableGenerator;

use Chamilo\Libraries\Utilities\Utilities;

/**
 * Component generator used to generate components
 *
 * @author Sven Vanpoucke
 */
class SortableTableGenerator
{

    private $template;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    public function generate_tables($default_location, $location, $application_name, $properties, $class, $author)
    {
        $this->template = new MyTemplate();
        $this->template->set_rootdir(__DIR__);

        if (! is_dir($default_location))
            mkdir($default_location, 0777, true);

        if (! is_dir($location))
            mkdir($location, 0777, true);

        $class_lower = (string) StringUtilities :: getInstance()->createString($class)->underscored();
        $class_upper = strtoupper($class_lower);

        $class2 = substr($class, - 1) == 'y' ? substr($class, 0, strlen($class) - 1) . 'ie' : $class;
        $class2 .= 's';
        $class2_lower = (string) StringUtilities :: getInstance()->createString($class2)->underscored();

        $files['default_table_cell_renderer'] = fopen(
            $default_location . 'default_' . $class_lower . '_table_cell_renderer.class.php',
            'w+');
        $files['default_table_column_model'] = fopen(
            $default_location . 'default_' . $class_lower . '_table_column_model.class.php',
            'w+');
        $files['table'] = fopen($location . $class_lower . '_browser_table.class.php', 'w+');
        $files['table_data_provider'] = fopen($location . $class_lower . '_browser_table_data_provider.class.php', 'w+');
        $files['table_cell_renderer'] = fopen($location . $class_lower . '_browser_table_cell_renderer.class.php', 'w+');
        $files['table_column_model'] = fopen($location . $class_lower . '_browser_table_column_model.class.php', 'w+');

        $boolean = true;
        foreach ($files as $file)
            $boolean &= ! is_null($file);

        if ($boolean)
        {
            $this->template->set_filenames(
                array(
                    'default_table_cell_renderer' => 'default_table_cell_renderer.template',
                    'default_table_column_model' => 'default_table_column_model.template',
                    'table' => 'table.template',
                    'table_data_provider' => 'table_data_provider.template',
                    'table_cell_renderer' => 'table_cell_renderer.template',
                    'table_column_model' => 'table_column_model.template'));

            $this->template->assign_vars(
                array(
                    'APPLICATION_NAME' => (string) StringUtilities :: getInstance()->createString($application_name)->upperCamelize(),
                    'L_APPLICATION_NAME' => (string) StringUtilities :: getInstance()->createString($application_name)->underscored(),
                    'L_OBJECT_CLASS' => $class_lower,
                    'OBJECT_CLASS' => $class,
                    'L_OBJECT_CLASSES' => $class2_lower,
                    'U_OBJECT_CLASS' => $class_upper,
                    'AUTHOR' => $author,
                    'NAMESPACE' => 'application\\' . $application_name));

            foreach ($properties as $property)
            {
                $this->template->assign_block_vars(
                    "PROPERTIES",
                    array('U_PROPERTY' => strtoupper($property), 'L_PROPERTY' => $property));
            }

            foreach ($files as $key => $file)
            {
                $string = trim($this->template->pparse_return($key));
                fwrite($file, $string);
                fclose($file);
            }
        }
    }
}
