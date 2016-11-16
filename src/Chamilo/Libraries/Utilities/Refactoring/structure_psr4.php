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
            if (strpos($php_file, '.class.php') !== false)
            {
                $content = file_get_contents($complete_path);
                $matches = array();
                
                $result = preg_match('/\nclass ([A-Za-z0-9_]*)/', $content, $matches);
                
                if ($result)
                {
                    $new_php_file = $matches[1] . '.php';
                    $new_complete_path = $folder . $new_php_file;
                    echo '<tr><td>Class</td><td>' . $complete_path . '</td><td>' . $new_complete_path . '</td></tr>';
                    
                    Filesystem::move_file($complete_path, $new_complete_path);
                }
                
                $result = preg_match('/\nabstract class ([A-Za-z0-9_]*)/', $content, $matches);
                
                if ($result)
                {
                    $new_php_file = $matches[1] . '.php';
                    $new_complete_path = $folder . $new_php_file;
                    echo '<tr><td>Class</td><td>' . $complete_path . '</td><td>' . $new_complete_path . '</td></tr>';
                    
                    Filesystem::move_file($complete_path, $new_complete_path);
                }
            }
            elseif (strpos($php_file, '.interface.php') !== false)
            {
                $content = file_get_contents($complete_path);
                $matches = array();
                
                $result = preg_match('/\ninterface ([A-Za-z0-9_]*)/', $content, $matches);
                
                if ($result)
                {
                    $new_php_file = $matches[1] . '.php';
                    $new_complete_path = $folder . $new_php_file;
                    echo '<tr><td>Class</td><td>' . $complete_path . '</td><td>' . $new_complete_path . '</td></tr>';
                    
                    Filesystem::move_file($complete_path, $new_complete_path);
                }
            }
            elseif (strpos($php_file, '.trait.php') !== false)
            {
                $content = file_get_contents($complete_path);
                $matches = array();
                
                $result = preg_match('/\ntrait ([A-Za-z0-9_]*)/', $content, $matches);
                
                if ($result)
                {
                    $new_php_file = $matches[1] . '.php';
                    $new_complete_path = $folder . $new_php_file;
                    echo '<tr><td>Class</td><td>' . $complete_path . '</td><td>' . $new_complete_path . '</td></tr>';
                    
                    Filesystem::move_file($complete_path, $new_complete_path);
                }
            }
            elseif (strpos($php_file, '.php') !== false)
            {
                $content = file_get_contents($complete_path);
                
                $matches = array();
                $result = preg_match('/\nclass ([A-Za-z0-9_]*)/', $content, $matches);
                
                if ($result)
                {
                    $new_php_file = $matches[1] . '.php';
                    $new_complete_path = $folder . $new_php_file;
                    
                    if ($new_php_file !== $php_file)
                    {
                        echo '<tr><td style="color:red;">Class</td><td>' . $complete_path . '</td><td>' .
                             $new_complete_path . '</td></tr>';
                        
                        Filesystem::move_file($complete_path, $new_complete_path);
                    }
                }
                
                $matches = array();
                $result = preg_match('/\nabstract class ([A-Za-z0-9_]*)/', $content, $matches);
                
                if ($result)
                {
                    $new_php_file = $matches[1] . '.php';
                    $new_complete_path = $folder . $new_php_file;
                    
                    if ($new_php_file !== $php_file)
                    {
                        echo '<tr><td style="color:red;">Class</td><td>' . $complete_path . '</td><td>' .
                             $new_complete_path . '</td></tr>';
                        
                        Filesystem::move_file($complete_path, $new_complete_path);
                    }
                }
                
                $matches = array();
                $result = preg_match('/\ninterface ([A-Za-z0-9_]*)/', $content, $matches);
                
                if ($result)
                {
                    $new_php_file = $matches[1] . '.php';
                    $new_complete_path = $folder . $new_php_file;
                    
                    if ($new_php_file !== $php_file)
                    {
                        echo '<tr><td style="color:red;">Class</td><td>' . $complete_path . '</td><td>' .
                             $new_complete_path . '</td></tr>';
                        
                        Filesystem::move_file($complete_path, $new_complete_path);
                    }
                }
                
                $matches = array();
                $result = preg_match('/\ntrait ([A-Za-z0-9_]*)/', $content, $matches);
                
                if ($result)
                {
                    $new_php_file = $matches[1] . '.php';
                    $new_complete_path = $folder . $new_php_file;
                    
                    if ($new_php_file !== $php_file)
                    {
                        echo '<tr><td style="color:red;">Class</td><td>' . $complete_path . '</td><td>' .
                             $new_complete_path . '</td></tr>';
                        
                        Filesystem::move_file($complete_path, $new_complete_path);
                    }
                }
            }
        }
    }
}

echo '<table border="1">';
process_folder($root);
echo '</table>';