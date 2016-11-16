<?php
namespace Chamilo\Libraries\Utilities\ApplicationGenerator;

use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Utilities\Utilities;

ini_set('include_path', realpath(__DIR__ . '/../../../configuration/plugin/pear'));

require_once __DIR__ . '/../../../../../libraries/architecture/php/lib/bootstrap.class.php';
\Chamilo\Libraries\Architecture\Bootstrap::getInstance()->setup();

include (__DIR__ . '/settings.inc.php');
include (__DIR__ . '/my_template.class.php');
include (__DIR__ . '/data_class_generator/data_class_generator.class.php');
include (__DIR__ . '/form_generator/form_generator.class.php');
include (__DIR__ . '/sortable_table_generator/sortable_table_generator.class.php');
include (__DIR__ . '/data_manager_generator/data_manager_generator.class.php');
include (__DIR__ . '/manager_generator/manager_generator.class.php');
include (__DIR__ . '/component_generator/component_generator.class.php');
include (__DIR__ . '/rights_generator/rights_generator.class.php');
include (__DIR__ . '/install_generator/install_generator.class.php');
include (__DIR__ . '/autoloader_generator/autoloader_generator.class.php');
include (__DIR__ . '/package_info_generator/package_info_generator.class.php');

$location = $application['location'] . '/php/';
$name = $application['name'];
$author = $application['author'];

$data_class_generator = new DataclassGenerator();
$form_generator = new FormGenerator();
$sortable_table_generator = new SortableTableGenerator();

// Create Folders
log_message('Creating folders...');
create_folders($location, $name);
log_message('Folders succesfully created.');

/**
 * Parse XML files Generate DataClasses Generate Forms
 */
log_message('Generating dataclasses, forms and tables...');
$files = Filesystem::get_directory_content($location . '../', Filesystem::LIST_FILES, false);
foreach ($files as $file)
{
    if (substr($file, - 4) != '.xml')
        continue;
    
    $new_path = move_file($location, $file);
    
    $properties = retrieve_properties_from_xml_file($location . '/../', $file);
    $lclass = str_replace('.xml', '', basename($file));
    $classname = (string) StringUtilities::getInstance()->createString($lclass)->upperCamelize();
    
    $description = 'This class describes a ' . $classname . ' data object';
    
    $data_class_generator->generate_data_class(
        $location . 'lib/', 
        $classname, 
        $properties, 
        $name, 
        $description, 
        $author, 
        $name);
    $form_generator->generate_form($location . 'lib/forms/', $classname, $properties, $author, $name);
    
    if ($application['options'][$lclass]['table'] == 1)
    {
        generate_sortable_table($location, $classname, $properties, $name, $author);
    }
    
    $classes[] = $classname;
}
log_message('Dataclasses and forms generated.');

// Generate the Data Managers
log_message('Generating data managers...');
generate_data_managers($location, $name, $classes, $author);
log_message('Data managers generated.');

// Generate the Managers
log_message('Generating managers...');
generate_managers($location, $name, $classes, $author);
log_message('Managers generated.');

// Generate the Components
log_message('Generating components...');
generate_components($location, $name, $classes, $author);
log_message('Components generated.');

// Generate Rights Files
log_message('Generating right files...');
generate_rights_files($location, $name);
log_message('Right files generated.');

// Generate Install Files
log_message('Generating install files...');
generate_install_files($location, $name, $author);
log_message('Install files generated.');

log_message('Generate autoloader...');
generate_autoloader($location, $name, $classes, $author, $application['options']);
log_message('Autoloader generated.');

log_message('Generating package info...');
generate_package_info($location, $name, $author);
log_message('Package info generated.');

/**
 * Create folders for the application
 * 
 * @param String $location - The location of the application
 * @param String $name - The name of the application
 */
function create_folders($location, $name)
{
    $folders = array(
        'lib/data_manager', 
        'lib/forms', 
        'install', 
        'lib/' . $name . '_manager', 
        'lib/' . $name . '_manager/component', 
        'rights', 
        'lib/tables');
    foreach ($folders as $folder)
    {
        Filesystem::create_dir($location . $folder);
    }
}

/**
 * Move a file from the root to the install folder
 * 
 * @param String $file - Path of the file
 * @return String $new_file - New path of the file
 */
function move_file($location, $file)
{
    $new_file = $location . 'install/' . basename($file);
    Filesystem::copy_file($location . '../' . $file, $new_file);
    return $new_file;
}

/**
 * Retrieves the properties from a data xml file
 * 
 * @param String $file - The xml file
 * @return Array of String - The properties
 */
function retrieve_properties_from_xml_file($location, $file)
{
    $properties = array();
    
    $options[] = array(XML_UNSERIALIZER_OPTION_FORCE_ENUM => array('property'));
    $array = Utilities::extract_xml_file($location . $file, $options);
    
    foreach ($array['properties']['property'] as $property)
    {
        $properties[] = $property['name'];
    }
    
    return $properties;
}

/**
 * Generates sortable tables for an application
 * 
 * @param String $location - The application location
 * @param String $classname - The class names
 * @param String $properties - The class properties
 * @param String $name - The application name
 * @param String $author - The Author
 */
function generate_sortable_table($location, $classname, $properties, $name, $author)
{
    $l_class = (string) StringUtilities::getInstance()->createString($classname)->underscored();
    
    $default_location = $location . 'lib/tables/' . $l_class . '_table/';
    $browser_table_location = $location . 'lib/' . $name . '_manager/component/' . $l_class . '_browser/';
    
    global $sortable_table_generator;
    $sortable_table_generator->generate_tables(
        $default_location, 
        $browser_table_location, 
        $name, 
        $properties, 
        $classname, 
        $author);
}

/**
 * Generates the data managers for an application
 * 
 * @param String $location - The application location
 * @param String $name - The application name
 * @param String $classes - The class names
 * @param String $author - The Author
 */
function generate_data_managers($location, $name, $classes, $author)
{
    $data_manager_location = $location . 'lib/';
    $database_location = $location . 'lib/data_manager/';
    $data_manager_generator = new DataManagerGenerator();
    $data_manager_generator->generate_data_managers(
        $data_manager_location, 
        $database_location, 
        $name, 
        $classes, 
        $author);
}

/**
 * Generates the managers for an application
 * 
 * @param String $location - The application location
 * @param String $name - The application name
 * @param String $classes - The class names
 * @param String $author - The Author
 */
function generate_managers($location, $name, $classes, $author)
{
    $manager_location = $location . 'lib/' .
         (string) StringUtilities::getInstance()->createString($name)->underscored() . '_manager/';
    $manager_generator = new ManagerGenerator();
    $manager_generator->generate_managers($manager_location, $name, $classes, $author);
}

/**
 * Generates the components for an application
 * 
 * @param String $location - The application location
 * @param String $name - The application name
 * @param String $classes - The class names
 * @param String $author - The Author
 */
function generate_components($location, $name, $classes, $author)
{
    $manager_location = $location . 'lib/' .
         (string) StringUtilities::getInstance()->createString($name)->underscored() . '_manager/component/';
    $component_generator = new ComponentGenerator();
    
    global $application;
    
    $component_generator->generate_components($manager_location, $name, $classes, $author, $application['options']);
}

/**
 * Generates rights files for an application
 * 
 * @param String $location - The application location
 * @param String $name - The application name
 */
function generate_rights_files($location, $name)
{
    $rights_location = $location . 'rights/';
    $rights_generator = new RightsGenerator();
    $rights_generator->generate_right_files($rights_location, $name);
}

/**
 * Generates install files for an application
 * 
 * @param String $location - The application location
 * @param String $name - The application name
 */
function generate_install_files($location, $name, $author)
{
    $install_location = $location . 'install/';
    $install_generator = new InstallGenerator();
    $install_generator->generate_install_files($install_location, $name, $author);
}

/**
 * Log a message to the screen
 * 
 * @param String $message - The message
 */
function log_message($message)
{
    $total_message = date('[H:m:s] ') . $message . '<br />';
    echo $total_message;
}

/**
 * Generates the autoloader for an application
 * 
 * @param String $location - The application location
 * @param String $name - The application name
 * @param String $classes - The class names
 * @param String $author - The Author
 */
function generate_autoloader($location, $name, $classes, $author, $options)
{
    $manager_generator = new AutoloaderGenerator();
    $manager_generator->generate_autoloader($location, $name, $classes, $author, $options);
}

/**
 * Generates the package info for an application
 * 
 * @param String $location - The application location
 * @param String $name - The application name
 * @param String $author - The Author
 */
function generate_package_info($location, $name, $author)
{
    $manager_generator = new PackageInfoGenerator();
    $manager_generator->generate_package_info($location, $name, $author);
}
