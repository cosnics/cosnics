<?php
namespace Chamilo\Libraries\Utilities\DataClassGenerator;

use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Properties\FileProperties;
use Chamilo\Libraries\Utilities\Utilities;
use DOMDocument;

require_once __DIR__ . '/../../Libraries/Architecture/Bootstrap.php';
\Chamilo\Libraries\Architecture\Bootstrap :: getInstance()->setup();

include (__DIR__ . '/settings.inc.php');
include (__DIR__ . '/my_template.class.php');
include (__DIR__ . '/data_class_generator/data_class_generator.class.php');

$author = $data_class['author'];
$package = $data_class['package'];
$application = $data_class['application'];

$data_class_generator = new DataClassGenerator();

$xml_path = __DIR__ . '/xml_schemas/';
$xml_files = Filesystem :: get_directory_content($xml_path, Filesystem :: LIST_FILES, false);

foreach ($xml_files as $xml_file)
{

    $xml_file_path = $xml_path . $xml_file;
    $properties = FileProperties :: from_path($xml_file_path);
    if ($properties->get_extension() == 'xml')
    {
        log_message('Start generating content object for: ' . $xml_file);
        log_message('Retrieving properties');
        $xml_definition = retrieve_properties_from_xml_file($xml_file_path);

        if (file_exists(
            __DIR__ . '/xml_schemas/php/' . ClassnameUtilities :: getInstance()->namespaceToPath($xml_definition['namespace']) .
                 '/php/lib/' . $xml_definition['name'] . '.class.php'))
        {
            log_message('Object type already exists');
        }
        else
        {
            $classname = (string) StringUtilities :: getInstance()->createString($xml_definition['name'])->upperCamelize();
            $description = 'This class describes a ' . $classname . ' data object';

            // dump($xml_definition);
            log_message('Generating data class');
            $data_class_generator->generate_data_class($xml_definition, $author, $package, $application);
        }

        echo '<hr />';
    }
}

exit();

/**
 * Create folders for the application
 *
 * @param $location String - The location of the application
 * @param $name String - The name of the application
 */
function create_folder($name)
{
    $location = Path :: get_repository_path() . 'lib/content_object/';
    Filesystem :: create_dir($location . $name);
}

/**
 * Move a file from the root to the install folder
 *
 * @param $file String - Path of the file
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
 * @param $file String - The xml file
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
    $namespace = $object->getAttribute('namespace');
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
    $result['namespace'] = $namespace;
    $result['properties'] = $properties;
    $result['indexes'] = $indexes;

    return $result;
}

/**
 * Log a message to the screen
 *
 * @param $message String - The message
 */
function log_message($message)
{
    $total_message = date('[H:m:s] ') . $message . '<br />';
    echo $total_message;
}
