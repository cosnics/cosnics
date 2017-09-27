<?php
namespace Chamilo\Libraries\Utilities\Various;

use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;

require_once realpath(__DIR__ . '/../../../../') . '/vendor/autoload.php';

$container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
$container->get('chamilo.libraries.architecture.bootstrap.bootstrap')->setup();

$root = Path::getInstance()->getBasePath();

$files = Filesystem::get_directory_content($root);

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
