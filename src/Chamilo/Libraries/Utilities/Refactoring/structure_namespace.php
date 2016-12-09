<?php
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
require __DIR__ . '/../../Architecture/Bootstrap.php';

Chamilo\Libraries\Architecture\Bootstrap::getInstance();

$root = Path::getInstance()->namespaceToFullPath('Chamilo\Application\CasUser');

function process_folder($folder)
{
    $blacklist = array('.hg', 'resources', 'plugin');
    $php_files = Filesystem::get_directory_content($folder, Filesystem::LIST_FILES_AND_DIRECTORIES, false);
    
    foreach ($php_files as $php_file)
    {
        $complete_path = $folder . $php_file;
        
        if (is_dir($complete_path))
        {
            if (! in_array($php_file, $blacklist))
            {
                process_folder($complete_path . '/');
            }
        }
        else
        {
            if (strpos($php_file, '.php') !== false)
            {
                $namespace = str_replace('C:\wamp\www\corec5\src/', '', $complete_path);
                
                $namespace = str_replace('\\', '/', $namespace);
                $namespace = explode('/', $namespace);
                array_pop($namespace);
                $namespace = implode('\\', $namespace);
                
                $namespace_string = 'namespace ' . $namespace . ';';
                
                $content = file_get_contents($complete_path);
                $content = preg_replace('/namespace .*;/', $namespace_string, $content, 1);
                
                file_put_contents($complete_path, $content);
            }
        }
    }
}

process_folder($root);