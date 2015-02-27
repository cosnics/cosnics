<?php
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Filesystem;
use Symfony\CS\Tokenizer\Tokens;
require __DIR__ . '/../../Architecture/Bootstrap.php';

Chamilo\Libraries\Architecture\Bootstrap :: getInstance();

$root = Path :: getInstance()->namespaceToFullPath('Ehb');

function process_folder($folder)
{
    $blacklist = array('.hg', 'Resources', 'resources', 'plugin', 'Plugin');
    $php_files = Filesystem :: get_directory_content($folder, Filesystem :: LIST_FILES_AND_DIRECTORIES, false);

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
                $tokens = Tokens :: fromCode($content);

                $in_use_statement = false;
                $first_token = null;
                $last_token = null;

                for ($index = 0; $index < count($tokens); $index ++)
                {
                    $token = $tokens[$index];

                    if ($token->isGivenKind(T_USE))
                    {
                        $in_use_statement = true;

                        if (! isset($first_token))
                        {
                            $first_token = $index;
                        }

                        continue;
                    }

                    $is_end_of_use_statement = ($token->getId() === null && $token->getContent() === ';');
                    $is_comment = $token->isGivenKind(T_COMMENT) || $token->isGivenKind(T_ML_COMMENT) ||
                         $token->isGivenKind(T_DOC_COMMENT);

                    if ($is_end_of_use_statement && $in_use_statement)
                    {
                        $in_use_statement = false;
                        $last_token = $index;
                        continue;
                    }

                    if ($token->isClassy() || $token->isComment() || $token->isEmpty() ||
                         $token->isGivenKind(T_REQUIRE_ONCE) || $token->isGivenKind(T_REQUIRE))
                    {
                        break;
                    }
                }

                if (! is_null($first_token) && ! is_null($last_token))
                {
                    $use_statements = $tokens->generatePartialCode($first_token, $last_token);
                    $use_statements = strtr($use_statements, "\r\n", "\n");
                    $use_statements = explode("\n", $use_statements);
                    sort($use_statements);

                    $use_statements = implode(PHP_EOL, $use_statements);

                    for ($index = $first_token; $index <= $last_token; $index ++)
                    {
                        $token = $tokens[$index];
                        $token->clear();
                    }

                    $use_tokens = Tokens :: fromCode($use_statements);
                    $tokens->insertAt($first_token, $use_tokens);

                    $tokens->removeLeadingWhitespace($first_token);
                    $whitespace_tokens = Tokens :: fromCode("\n\n");
                    $tokens->insertAt($first_token, $whitespace_tokens);

                    file_put_contents($complete_path, $tokens->generateCode());

                    echo $complete_path . "\r\n";
                    flush();
                    ob_flush();
                }
            }
        }
    }
}

header('Content-Type: text/plain');

process_folder($root);