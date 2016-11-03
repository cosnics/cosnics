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
        if (is_null($string))
        {
            return true;
        }

        if (! is_string($string))
        {
            return false;
        }

        if ($forHumans)
        {
            $string = trim(str_replace('&nbsp;', '', strip_tags($string)));
        }

        if (strlen($string) === 0)
        {
            return true;
        }

        return false;
    }

    /**
     *
     * @param string $email
     * @param string $clickable_text
     * @param string $style_class
     * @return string
     */
    public function encryptMailLink($email, $clickable_text = null, $style_class = '')
    {
        if (is_null($clickable_text))
        {
            $clickable_text = $email;
        }
        // mailto already present?
        if (substr($email, 0, 7) != 'mailto:')
            $email = 'mailto:' . $email;
            // class (stylesheet) defined?
        if ($style_class != '')
        {
            $style_class = ' class="full_url_print ' . $style_class . '"';
        }
        else
        {
            $style_class = ' class="full_url_print"';
        }
        // encrypt email
        $hmail = '';
        for ($i = 0; $i < strlen($email); $i ++)
            $hmail .= '&#' . ord($email{$i}) . ';';
            // encrypt clickable text if @ is present
        if (strpos($clickable_text, '@'))
        {
            for ($i = 0; $i < strlen($clickable_text); $i ++)
                $hclickable_text .= '&#' . ord($clickable_text{$i}) . ';';
        }
        else
        {
            $hclickable_text = htmlspecialchars($clickable_text);
        }
        // return encrypted mailto hyperlink
        return '<a href="' . $hmail . '"' . $style_class . '>' . $hclickable_text . '</a>';
    }

    /**
     *
     * @param string $string
     * @param integer $length
     * @param boolean $stripTags
     * @param string $character
     * @return string
     */
    public function truncate($string, $length = 200, $stripTags = true, $character = "\xE2\x80\xA6")
    {
        if ($stripTags)
        {
            $string = strip_tags($string);
        }

        return (string) $this->createString($string)->truncate($length, $character);
    }
}
