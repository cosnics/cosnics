<?php


use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
ini_set('include_path', realpath(__DIR__ . '/../../../configuration/plugin/pear'));
ini_set('max_execution_time', - 1);
require_once __DIR__ . '/../../filesystem/path.class.php';

//$SEARCH = 'content_object';
//$REPLACE = 'learning_object';
$SEARCH = 'learning_object';
$REPLACE = 'content_object';

$path = Path :: getInstance()->getBasePath();
//$path = __DIR__;
$files = Filesystem :: get_directory_content($path, $type = Filesystem :: LIST_FILES_AND_DIRECTORIES, true);
foreach ($files as $file)
{
    if (strpos($file, $SEARCH) !== false && strpos($file, '.svn') === false)
    {
        $new_file = str_replace($SEARCH, $REPLACE, $file);

        if (is_dir($file))
        {
            mkdir($new_file);
            $removedirs[] = $file;
        }
        else
        {
            rename($file, $new_file);
        }

        echo 'Renamed ' . $file . ' to ' . $new_file . '<br />';
    }
}

foreach ($removedirs as $dir)
    rmdir($dir);

/**
 * Log a message to the screen
 * @param String $message - The message
 */
function log_message($message)
{
    $total_message = date('[H:m:s] ') . $message . '<br />';
    echo $total_message;
}
