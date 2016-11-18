<?php
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\CS\Tokenizer\Tokens;
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
                $content = file_get_contents($complete_path);
                $tokens = Tokens::fromCode($content);
                
                $in_namespace = false;
                $in_use = false;
                $is_inline = false;
                
                for ($index = 0; $index < count($tokens); $index ++)
                {
                    $token = $tokens[$index];
                    
                    if ($token->isGivenKind(T_NAMESPACE))
                    {
                        $in_namespace = true;
                        continue;
                    }
                    
                    if ($token->isGivenKind(T_USE) && ! $in_namespace)
                    {
                        $in_use = true;
                        continue;
                    }
                    
                    if ($token->isGivenKind(T_NS_SEPARATOR) && ! $in_namespace && ! $in_use && ! $is_inline)
                    {
                        $is_inline = true;
                        $token->setContent('\Chamilo\\');
                        continue;
                    }
                    
                    if ($token->isGivenKind(T_WHITESPACE) && ($in_namespace || (! $in_namespace && $in_use)))
                    {
                        if ($token->isGivenKind(T_WHITESPACE) && $tokens[$index - 1]->isGivenKind(T_STRING))
                        {
                            $in_use = false;
                            continue;
                        }
                        else
                        {
                            $token->setContent(' Chamilo\\');
                            continue;
                        }
                    }
                    
                    if ($token->isGivenKind(T_STRING) &&
                         ($in_namespace || (! $in_namespace && $in_use) || (! $in_namespace && ! $in_use && $is_inline)))
                    {
                        $token->setContent(
                            (string) StringUtilities::getInstance()->createString($token->getContent())->upperCamelize());
                        continue;
                    }
                    
                    if (($token->getId() === null && $token->getContent() === ';'))
                    {
                        if ($in_namespace)
                        {
                            $in_namespace = false;
                            continue;
                        }
                        
                        if (! $in_namespace && $in_use)
                        {
                            $in_use = false;
                            continue;
                        }
                    }
                    
                    if (! $in_namespace && ! $in_use && $is_inline)
                    {
                        if (! $token->isGivenKind(T_STRING) && ! $token->isGivenKind(T_NS_SEPARATOR))
                        {
                            $is_inline = false;
                            continue;
                        }
                    }
                }
                
                file_put_contents($complete_path, $tokens->generateCode());
                
                echo $complete_path . "\r\n";
                flush();
                ob_flush();
            }
        }
    }
}

header('Content-Type: text/plain');

process_folder($root);