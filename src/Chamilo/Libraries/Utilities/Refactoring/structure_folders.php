<?php


use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Utilities\StringUtilities;
require __DIR__ . '/../../Architecture/Bootstrap.php';

Chamilo\Libraries\Architecture\Bootstrap :: getInstance();

$root = Path :: getInstance()->namespaceToFullPath('Chamilo\Application\CasUser');

function process_folder($folder)
{
    $blacklist = array('.hg', 'plugin');
    $php_files = Filesystem :: get_directory_content($folder, Filesystem :: LIST_DIRECTORIES, false);

    foreach ($php_files as $php_file)
    {
        $complete_path = $folder . $php_file;

        if (is_dir($complete_path))
        {
            if (! in_array($php_file, $blacklist))
            {
                process_folder($complete_path . '/');

                $complete_new_path = $folder . StringUtilities :: getInstance()->createString($php_file)->upperCamelize();

                rename($complete_path, $complete_new_path);
            }
        }
    }
}

process_folder($root);