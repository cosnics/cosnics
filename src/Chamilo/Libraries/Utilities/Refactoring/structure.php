<?php


use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
require __DIR__ . '/../../Architecture/Bootstrap.php';

Chamilo\Libraries\Architecture\Bootstrap :: getInstance();

$root = Path :: getInstance()->namespaceToFullPath('Chamilo\Application\CasUser');

function process_folder($folder)
{
    $source_folders = Filesystem :: get_directory_content($folder, Filesystem :: LIST_DIRECTORIES, false);

    foreach ($source_folders as $source_folder)
    {
        $blacklist = array('.hg', 'resources', 'plugin');

        if (! in_array($source_folder, $blacklist))
        {
            if ($source_folder == 'php')
            {
                $source_path = $folder . 'php/';
                $code_files = Filesystem :: get_directory_content(
                    $source_path,
                    Filesystem :: LIST_FILES_AND_DIRECTORIES,
                    false);

                foreach ($code_files as $code_file)
                {
                    if ($code_file == 'autoloader.class.php')
                    {
                        Filesystem :: remove($source_path . '/autoloader.class.php');
                    }
                    elseif ($code_file == 'lib')
                    {
                        $lib_path = $source_path . 'lib/';
                        $lib_files = Filesystem :: get_directory_content(
                            $lib_path,
                            Filesystem :: LIST_FILES_AND_DIRECTORIES,
                            false);

                        foreach ($lib_files as $lib_file)
                        {
                            if ($lib_file == 'manager')
                            {
                                // Move manager file
                                $lib_source_path = $lib_path . $lib_file . '/manager.class.php';
                                $lib_destination_path = $folder . 'manager.class.php';

                                // var_dump($lib_source_path . ' >> ' . $lib_destination_path);
                                Filesystem :: recurse_move($lib_source_path, $lib_destination_path);

                                // Move components folder
                                $lib_source_path = $lib_path . $lib_file . '/component/';
                                $lib_destination_path = $folder . 'component/';

                                // var_dump($lib_source_path . ' >> ' . $lib_destination_path);
                                Filesystem :: recurse_move($lib_source_path, $lib_destination_path);

                                // Move application folder
                                $lib_source_path = $lib_path . $lib_file . '/application/';
                                $lib_destination_path = $folder . 'component/';

                                // var_dump($lib_source_path . ' >> ' . $lib_destination_path);
                                Filesystem :: recurse_move($lib_source_path, $lib_destination_path);
                            }
                            else
                            {
                                // Move folders / files
                                $lib_source_path = $lib_path . $lib_file;
                                $lib_destination_path = $folder . $lib_file;

                                // var_dump($lib_source_path . ' >> ' . $lib_destination_path);
                                Filesystem :: recurse_move($lib_source_path, $lib_destination_path);
                            }
                        }
                    }
                    elseif ($code_file == 'settings')
                    {
                        $code_source_path = $source_path . $code_file;
                        $code_destination_path = $folder . 'resources/' . $code_file;

                        // var_dump($code_source_path . ' >> ' . $code_destination_path);
                        Filesystem :: recurse_move($code_source_path, $code_destination_path);
                    }
                    elseif ($code_file == 'package')
                    {
                        // Move package.info
                        $package_info_source_path = $source_path . 'package/package.info';
                        $package_info_destination_path = $folder . 'package.info';

                        // var_dump($package_info_source_path . ' >> ' . $package_info_destination_path);
                        Filesystem :: recurse_move($package_info_source_path, $package_info_destination_path);

                        // Move XML files
                        $storage_xml_path = $source_path . 'package/install/';
                        $storage_xml_files = Filesystem :: get_directory_content(
                            $storage_xml_path,
                            Filesystem :: LIST_FILES,
                            false);

                        foreach ($storage_xml_files as $storage_xml_file)
                        {
                            if (strpos($storage_xml_file, '.xml') !== false)
                            {
                                $storage_xml_source_path = $storage_xml_path . $storage_xml_file;
                                $storage_xml_destination_path = $folder . 'resources/storage/' . $storage_xml_file;

                                // var_dump($storage_xml_source_path . ' >> ' . $storage_xml_destination_path);
                                Filesystem :: recurse_move($storage_xml_source_path, $storage_xml_destination_path);
                            }
                        }

                        // Move action classes
                        $action_source_path = $source_path . 'package/install/installer.class.php';
                        $action_destination_path = $folder . 'package/installer.class.php';

                        // var_dump($action_source_path . ' >> ' . $action_destination_path);
                        Filesystem :: recurse_move($action_source_path, $action_destination_path);

                        $action_source_path = $source_path . 'package/activate/activator.class.php';
                        $action_destination_path = $folder . 'package/activator.class.php';

                        // var_dump($action_source_path . ' >> ' . $action_destination_path);
                        Filesystem :: recurse_move($action_source_path, $action_destination_path);

                        $action_source_path = $source_path . 'package/deactivate/deactivator.class.php';
                        $action_destination_path = $folder . 'package/deactivator.class.php';

                        // var_dump($action_source_path . ' >> ' . $action_destination_path);
                        Filesystem :: recurse_move($action_source_path, $action_destination_path);

                        $action_source_path = $source_path . 'package/remove/remover.class.php';
                        $action_destination_path = $folder . 'package/remover.class.php';

                        // var_dump($action_source_path . ' >> ' . $action_destination_path);
                        Filesystem :: recurse_move($action_source_path, $action_destination_path);
                    }
                    else
                    {
                        $code_source_path = $source_path . $code_file;
                        $code_destination_path = $folder . $code_file;

                        // var_dump($code_source_path . ' >> ' . $code_destination_path);
                        Filesystem :: recurse_move($code_source_path, $code_destination_path);
                    }
                }
            }
            elseif ($source_folder == 'build')
            {
                Filesystem :: remove($folder . '/' . $source_folder);
            }
            else
            {
                $new_folder = $folder . $source_folder . '/';
                process_folder($new_folder);
            }
        }
    }
}

process_folder($root);

exit();

// $data = array();

// $registrations = Configuration :: registrations();

// foreach ($registrations as $registration_type => $packages)
// {
// foreach ($packages as $blah)
// {
// foreach ($blah as $package)
// {
// $context = $package->get_context();
// $manager_class = $context . '\Manager';

// if (class_exists($manager_class))
// {
// $reflection = new ReflectionClass($manager_class);
// $constants = $reflection->getConstants();

// foreach ($constants as $constant => $value)
// {
// if (substr($constant, 0, 7) == 'ACTION_')
// {
// $component_path = Path :: getInstance()->namespaceToFullPath($context) . 'php/lib/manager/component/' .
// $value . '.class.php';
// if (! file_exists($component_path))
// {
// $data[] = array($context, $constant, $value);
// }
// }
// }
// }
// }
// }
// }

// $action_table = new SortableTableFromArray($data, 0, 600);
// $action_table->set_header(0, 'context');
// $action_table->set_header(1, 'constant');
// $action_table->set_header(2, 'component');

// $data = array();

// $registrations = Configuration :: registrations();

// foreach ($registrations as $registration_type => $packages)
// {
//     foreach ($packages as $blah)
//     {
//         foreach ($blah as $package)
//         {
//             $context = $package->get_context();
//             $manager_class = $context . '\Manager';

//             if (class_exists($manager_class))
//             {
//                 $reflection = new ReflectionClass($manager_class);
//                 $constants = $reflection->getConstants();

//                 $components_path = Path :: getInstance()->namespaceToFullPath($context) . 'php/lib/manager/component/';

//                 if (is_dir($components_path))
//                 {
//                     $files = Filesystem :: get_directory_content($components_path, Filesystem :: LIST_FILES, false);

//                     foreach ($files as $file)
//                     {
//                         $path_info = pathinfo($file);

//                         if ($path_info['extension'] == 'php')
//                         {
//                             if (strpos($path_info['filename'], '.class') !== false)
//                             {
//                                 $filename = str_replace('.class', '', $path_info['filename']);

//                                 if (! in_array($filename, $constants))
//                                 {
//                                     $data[] = array($context, $path_info['basename']);
//                                 }
//                             }
//                         }
//                     }
//                 }
//             }
//         }
//     }
// }

// $component_table = new SortableTableFromArray($data, 0, 600);
// $component_table->set_header(0, 'context');
// $component_table->set_header(1, 'component');

// Display :: small_header();
// // echo $action_table->toHtml();
// echo $component_table->toHtml();
// Display :: small_footer();