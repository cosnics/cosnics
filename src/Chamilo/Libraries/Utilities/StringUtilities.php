<?php
namespace Chamilo\Libraries\Utilities;

/**
 *
 * @package Chamilo\Libraries\Utilities
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class StringUtilities
{

    /**
     *
     * @var \Chamilo\Libraries\Utilities\StringUtilities
     */
    protected static $instance = null;

    /**
     *
     * @var string
     */
    private $encoding;

    /**
     *
     * @param string $encoding
     */
    public function __construct($encoding = 'UTF-8')
    {
        $this->encoding = $encoding;
    }

    /**
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     *
     * @param string $encoding
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    /**
     * Create a \Stringy\Stringy instance with the given string and return it
     * 
     * @param string $string
     * @return \Stringy\Stringy
     */
    public function createString($string)
    {
        return \Stringy\Stringy :: create($string, $this->encoding);
    }

    /**
     * Get an instance of StringUtilities
     * 
     * @return \Chamilo\Libraries\Utilities\StringUtilities
     */
    public static function getInstance()
    {
        if (is_null(static :: $instance))
        {
            self :: $instance = new static();
        }
        
        return static :: $instance;
    }

    /**
     *
     * @param string $string
     * @param boolean $forHumans
     * @return boolean
     */
    public function hasValue($string, $forHumans = false)
    {
        return ! $this->isNullOrEmpty($string, $forHumans);
    }

    /**
     *
     * @param string $string
     * @param boolean $forHumans
     * @return boolean
     */
    public function isNullOrEmpty($string, $forHumans = false)
    {
        if ($forHumans)
        {
            $tags = array('br', 'p', 'div', 'span');
            
            foreach ($tags as $tag)
            {
                $string = preg_replace('#</?' . $tag . '(>|\s[^>]*>)#is', '', $string);
            }
            
            $string = trim(str_replace('&nbsp;', '', $string));
        }
        
        if (isset($string))
        {
            if (is_string($string))
            {
                if (strlen($string) == 0)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            return true;
        }
    }
}
