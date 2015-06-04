<?php
namespace Chamilo\Libraries\Utilities\ContentObjectGenerator;

use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Utilities\Utilities;
use DOMDocument;

ini_set('include_path', realpath(__DIR__ . '/../../../configuration/plugin/pear'));

require_once __DIR__ . '/../../../../libraries/php/architecture/bootstrap.class.php';
\Chamilo\Libraries\Architecture\Bootstrap :: getInstance()->setup();

include (__DIR__ . '/settings.inc.php');
include (__DIR__ . '/my_template.class.php');
// include (Path :: getInstance()->getPluginPath() . 'phpbb/phpbb2_template.class.php');
include (__DIR__ . '/data_class_generator/data_class_generator.class.php');
include (__DIR__ . '/additional_class_generator/additional_class_generator.class.php');
include (__DIR__ . '/package_info_generator/package_info_generator.class.php');
include (__DIR__ . '/form_generator/form_generator.class.php');

$author = $content_object['author'];

$data_class_generator = new DataClassGenerator();
$additional_class_generator = new AdditionalClassGenerator();
$package_info_generator = new PackageInfoGenerator();
$form_generator = new FormGenerator();

$xml_path = __DIR__ . '/xml_schemas/';
$xml_files = Filesystem :: get_directory_content($xml_path, Filesystem :: LIST_FILES, false);

foreach ($xml_files as $xml_file)
{

    $xml_file_path = $xml_path . $xml_file;
    log_message('Start generating content object for: ' . $xml_file);
    log_message('Retrieving properties');
    $xml_definition = retrieve_properties_from_xml_file($xml_file_path);

    if (file_exists(
        Path :: get_repository_path() . 'lib/content_object/' . $xml_definition['name'] . '/install/' .
             $xml_definition['name'] . '.xml'))
    {
        log_message('Object type already exists');
    }
    else
    {
        log_message('Creating folder: ' . $xml_definition['name']);
        create_folder($xml_definition['name']);
        log_message('Moving XML file');
        $new_path = move_file($xml_definition['name']);

        $classname = (string) StringUtilities :: getInstance()->createString($xml_definition['name'])->upperCamelize();
        $description = 'This class describes a ' . $classname . ' data object';

        // dump($xml_definition);
        log_message('Generating data class');
        $data_class_generator->generate_data_class($xml_definition, $author);

        log_message('Generating package.info');
        $package_info_generator->generate_package_info($xml_definition, $author);

        log_message('Generating settings.xml');
        $package_info_generator->generate_settings($xml_definition);

        $additional_class_generator->set_xml_definition($xml_definition);
        $additional_class_generator->set_author($author);

        log_message('Generating data class display');
        $additional_class_generator->generate_data_class_display();

        log_message('Generating data class difference');
        $additional_class_generator->generate_data_class_difference();

        log_message('Generating data class difference display');
        $additional_class_generator->generate_data_class_difference_display();

        log_message('Generating complex data class');
        $additional_class_generator->generate_complex_data_class();

        log_message('Generating complex data class form');
        $additional_class_generator->generate_complex_data_class_form();

        log_message('Generating data class installer');
        $additional_class_generator->generate_data_class_installer();

        log_message('Generating data class form');
        $form_generator->generate_form($xml_definition, $author);
    }

    echo '<hr />';
}

exit();

// Create Folders
log_message('Creating folder');
create_folder($location, $name);
log_message('Folders succesfully created.');

/**
 * Parse XML files Generate DataClasses Generate Forms
 */
log_message('Generating dataclasses, forms and tables...');
$files = Filesystem :: get_directory_content($location, Filesystem :: LIST_FILES, false);
foreach ($files as $file)
{
    if (substr($file, - 4) != '.xml')
        continue;

    $new_path = move_file($location, $file);

    $properties = retrieve_properties_from_xml_file($location, $file);
    $lclass = str_replace('.xml', '', basename($file));
    $classname = (string) StringUtilities :: getInstance()->createString($lclass)->upperCamelize();

    $data_class_generator->generate_data_class($location, $classname, $properties, $name, $description, $author, $name);
    $form_generator->generate_form($location . 'forms/', $classname, $properties, $author);

    $classes[] = $classname;
}
log_message('Dataclasses and forms generated.');

/**
 * Create folders for the application
 *
 * @param String $location - The location of the application
 * @param String $name - The name of the application
 */
function create_folder($name)
{
    $location = Path :: get_repository_path() . 'lib/content_object/';
    Filesystem :: create_dir($location . $name);
}

/**
 * Move a file from the root to the install folder
 *
 * @param String $file - Path of the file
 * @return String $new_file - New path of the file
 */
function move_file($name)
{
    $old_file = __DIR__ . '/xml_schemas/' . $name . '.xml';
    $new_file = Path :: get_repository_path() . 'lib/content_object/' . $name . '/install/' . $name . '.xml';
    Filesystem :: copy_file($old_file, $new_file);
    return $new_file;
}

/**
 * Retrieves the properties from a data xml file
 *
 * @param String $file - The xml file
 * @return Array of String - The properties
 */
function retrieve_properties_from_xml_file($file)
{
    $name = '';
    $properties = array();
    $indexes = array();

    $doc = new DOMDocument();
    $doc->load($file);
    $object = $doc->getElementsByTagname('object')->item(0);
    $name = $object->getAttribute('name');
    $xml_properties = $doc->getElementsByTagname('property');
    $attributes = array('type', 'length', 'unsigned', 'notnull', 'default', 'autoincrement', 'fixed');
    foreach ($xml_properties as $index => $property)
    {
        $property_info = array();
        foreach ($attributes as $index => $attribute)
        {
            if ($property->hasAttribute($attribute))
            {
                $property_info[$attribute] = $property->getAttribute($attribute);
            }
        }
        $properties[$property->getAttribute('name')] = $property_info;
    }
    $xml_indexes = $doc->getElementsByTagname('index');
    foreach ($xml_indexes as $key => $index)
    {
        $index_info = array();
        $index_info['type'] = $index->getAttribute('type');
        $index_properties = $index->getElementsByTagname('indexproperty');
        foreach ($index_properties as $subkey => $index_property)
        {
            $index_info['fields'][$index_property->getAttribute('name')] = array(
                'length' => $index_property->getAttribute('length'));
        }
        $indexes[$index->getAttribute('name')] = $index_info;
    }
    $result = array();
    $result['name'] = $name;
    $result['properties'] = $properties;
    $result['indexes'] = $indexes;

    return $result;
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
