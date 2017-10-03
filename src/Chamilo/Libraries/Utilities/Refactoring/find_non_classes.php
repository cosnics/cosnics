<?php
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Symfony\CS\Tokenizer\Tokens;

require_once realpath(__DIR__ . '/../../../../') . '/vendor/autoload.php';

$container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
$container->get('chamilo.libraries.architecture.bootstrap.bootstrap')->setup();

$root = Path::getInstance()->namespaceToFullPath('Chamilo');

function process_folder($folder)
{
    $blacklist = array('.hg', 'Resources', 'resources', 'plugin', 'Plugin');
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
                $content = file_get_contents($complete_path);
                $tokens = Tokens::fromCode($content);

                $isClassy = false;

                for ($index = 0; $index < count($tokens); $index ++)
                {
                    $token = $tokens[$index];

                    if ($token->isClassy())
                    {
                        $isClassy = true;
                        break;
                    }
                }

                if (! $isClassy)
                {
                    echo $complete_path . "\r\n";
                }

                flush();
                ob_flush();
            }
        }
    }
}

header('Content-Type: text/plain');

process_folder($root);