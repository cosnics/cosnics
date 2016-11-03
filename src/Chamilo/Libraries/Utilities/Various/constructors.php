<?php
namespace Chamilo\Libraries\Utilities\Various;

use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;

require_once __DIR__ . '/../../Architecture/Bootstrap.php';
\Chamilo\Libraries\Architecture\Bootstrap :: getInstance()->setup();

$root = Path :: getInstance()->getBasePath();

$files = Filesystem :: get_directory_content($root);

foreach ($files as $file)
{
    if (is_dir($file))
    {
        continue;
    }

    if (substr($file, - 4) == '.php')
    {
        $contents = file_get_contents($file);
        $regex = '/class [a-zA-Z0-9_-]*/';
        preg_match_all($regex, $contents, $matches);

        foreach ($matches[0] as $match)
        {
            $class = substr($match, 6);

            if ($class && strpos($contents, 'function ' . $class . '(') !== false)
            {
                $contents = str_replace('function ' . $class . '(', 'function __construct(', $contents);
                var_dump('Changed class ' . $class);
            }
        }

        file_put_contents($file, $contents);
    }
}
