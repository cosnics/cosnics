<?php


use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
require __DIR__ . '/../../Architecture/Bootstrap.php';

Chamilo\Libraries\Architecture\Bootstrap :: getInstance();

$root = Path :: getInstance()->namespaceToFullPath('Chamilo');

function process_folder($folder)
{
    $source_folders = Filesystem :: get_directory_content($folder, Filesystem :: LIST_DIRECTORIES, false);

    foreach ($source_folders as $source_folder)
    {
        $blacklist = array('.hg', 'plugin');

        if (! in_array($source_folder, $blacklist))
        {
            if ($source_folder == 'Resources')
            {
                $themes = array('Aqua', 'Eagle', 'Ruby', 'Vub', 'Wine');

                foreach ($themes as $theme)
                {
                    $theme_css = $folder . 'Resources/Css/' . $theme . '/' . strtolower($theme) . '.css';
                    $new_theme_css = $folder . 'Resources/Css/' . $theme . '/Stylesheet.css';

                    if (file_exists($theme_css))
                    {
                        Filesystem :: recurse_move($theme_css, $new_theme_css);
                    }
                }
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
