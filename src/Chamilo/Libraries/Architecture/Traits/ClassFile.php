<?php
namespace Chamilo\Libraries\Architecture\Traits;

/**
 *
 * @package Chamilo\Libraries\Architecture\Traits
 */
trait ClassFile
{

    /**
     * Returns the classname from the given php file
     *
     * @param string $file
     * @return string
     */
    protected function getClassNameFromPHPFile($file)
    {
        $fp = fopen($file, 'r');
        $class = $buffer = '';
        $i = 0;

        $inNamespace = false;

        while (! feof($fp))
        {
            $buffer .= fread($fp, 512);
            $tokens = @token_get_all($buffer);

            if (strpos($buffer, '{') === false)
            {
                continue;
            }

            for (; $i < count($tokens); $i ++)
            {
                if ($tokens[$i][0] === T_NAMESPACE)
                {
                    $inNamespace = true;
                }

                if ($tokens[$i][0] === T_STRING)
                {
                    if ($inNamespace)
                    {
                        $class .= $tokens[$i][1];
                    }
                }

                if ($tokens[$i][0] === T_NS_SEPARATOR)
                {
                    if ($inNamespace)
                    {
                        $class .= $tokens[$i][1];
                    }
                }

                if ($tokens[$i] === ';')
                {
                    if ($inNamespace)
                    {
                        $class .= '\\';
                        $inNamespace = false;
                    }
                }

                if ($tokens[$i][0] === T_CLASS)
                {
                    for ($j = $i + 1; $j < count($tokens); $j ++)
                    {
                        if ($tokens[$j] === '{')
                        {
                            fclose($fp);
                            $class .= $tokens[$i + 2][1];
                            return $class;
                        }
                    }
                }
            }
        }

        fclose($fp);
    }
}
