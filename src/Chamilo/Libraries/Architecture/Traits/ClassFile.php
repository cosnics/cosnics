<?php
namespace Chamilo\Libraries\Architecture\Traits;

trait ClassFile
{

    /**
     * Returns the classname from the given php file
     * 
     * @param string $file
     *
     * @return string
     */
    protected function get_classname_from_php_file($file)
    {
        $fp = fopen($file, 'r');
        $class = $buffer = '';
        $i = 0;
        
        while (! $class)
        {
            if (feof($fp))
            {
                break;
            }
            
            $buffer .= fread($fp, 512);
            $tokens = @token_get_all($buffer);
            
            if (strpos($buffer, '{') === false)
            {
                continue;
            }
            
            for (; $i < count($tokens); $i ++)
            {
                if ($tokens[$i][0] === T_CLASS)
                {
                    for ($j = $i + 1; $j < count($tokens); $j ++)
                    {
                        if ($tokens[$j] === '{')
                        {
                            fclose($fp);
                            return $tokens[$i + 2][1];
                        }
                    }
                }
            }
        }
        
        fclose($fp);
    }
}
