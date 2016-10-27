<?php
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

require_once __DIR__ . '/../../Architecture/Bootstrap.php';
\Chamilo\Libraries\Architecture\Bootstrap :: getInstance()->setup();

$packages = \Chamilo\Configuration\Storage\DataManager :: retrieves(
    \Chamilo\Configuration\Storage\DataClass\Registration :: class_name(),
    new DataClassRetrievesParameters())->as_array();

// July 1, 2013 00:00:00
$unix_timestamp = 1372636800;
$base_command = 'hg log --template "{date|hgdate}\n" ';
$base_single_command = $base_command . '-l 1 ';

// Collections
$new_packages = array();
$new_tables = array();
$changed_tables = array();

foreach ($packages as $package)
{
    $context = $package->get_context();
    $folder = \Chamilo\Libraries\File\Path :: getInstance()->namespaceToFullPath($context);
    $package_folder = $folder . 'php' . DIRECTORY_SEPARATOR . 'package' . DIRECTORY_SEPARATOR;

    $package_info_path = $package_folder . 'package.info';

    $command = $base_command . $package_info_path;
    exec('cd ' . $folder . ' && ' . $command, $result);

    $date = (int) array_shift(explode(' ', array_pop($result)));

    if ($date > $unix_timestamp)
    {
        $new_packages[] = $package->get_context();
    }

    $xml_folder = $package_folder . 'install' . DIRECTORY_SEPARATOR;

    foreach (Filesystem :: get_directory_content($xml_folder, Filesystem :: LIST_FILES) as $file_path)
    {
        $path_parts = pathinfo($file_path);

        if ($path_parts['extension'] == 'xml')
        {
            $command = $base_command . $file_path;
            exec('cd ' . $folder . ' && ' . $command, $result);

            $dates = explode(' ', array_pop($result));

            $first_date = (int) array_shift($dates);
            $last_date = (int) array_pop($dates);

            if ($first_date > $unix_timestamp)
            {
                $new_tables[$package->get_context()][] = $path_parts['basename'];
            }
            elseif ($last_date > $unix_timestamp)
            {
                $changed_tables[$package->get_context()][] = $path_parts['basename'];
            }
        }
    }
}

sort($new_packages);
ksort($new_tables);
ksort($changed_tables);

echo '<h3>New Packages</h3>';

var_dump($new_packages);

echo '<h3>New Tables</h3>';

var_dump($new_tables);

echo '<h3>Changed Tables</h3>';

var_dump($changed_tables);