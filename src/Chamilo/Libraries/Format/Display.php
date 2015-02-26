<?php
namespace Chamilo\Libraries\Format;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Banner;
use Chamilo\Libraries\Format\Structure\Footer;
use Chamilo\Libraries\Format\Structure\Header;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Platform\Session\Session;

/**
 *
 * @package Chamilo\Libraries\Format
 * @author Roan Embrechts
 * @author Tim De Pauw
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Display
{
    const MESSAGE_TYPE_CONFIRM = 'confirm';
    const MESSAGE_TYPE_NORMAL = 'normal';
    const MESSAGE_TYPE_WARNING = 'warning';
    const MESSAGE_TYPE_ERROR = 'error';
    const MESSAGE_TYPE_FATAL = 'fatal';

    /**
     *
     * @param string $message
     * @return string
     */
    public static function normal_message($message)
    {
        return self :: message(self :: MESSAGE_TYPE_NORMAL, $message);
    }

    /**
     *
     * @param string $message
     * @return string
     */
    public static function normal_message_page($message)
    {
        $html = array();

        $html[] = self :: header();
        $html[] = self :: normal_message($message);
        $html[] = self :: footer();

        return implode("\n", $html);
    }

    /**
     *
     * @param string $message
     * @return string
     */
    public static function error_message($message)
    {
        return self :: message(self :: MESSAGE_TYPE_ERROR, $message);
    }

    /**
     *
     * @param string $message
     * @return string
     */
    public static function error_page($message)
    {
        $html = array();
        $html[] = self :: header();
        $html[] = self :: error_message($message);
        $html[] = self :: footer();

        return implode("\n", $html);
    }

    /**
     *
     * @param string $message
     * @return string
     */
    public static function warning_message($message)
    {
        return self :: message(self :: MESSAGE_TYPE_WARNING, $message);
    }

    /**
     *
     * @param string $type
     * @param string $message
     * @return string
     */
    public static function message($type = self :: MESSAGE_TYPE_NORMAL, $message)
    {
        $html = array();

        $html[] = '<div class="' . $type . '-message">';
        $html[] = $message;
        $html[] = '<div class="close_message" id="closeMessage"></div>';
        $html[] = '</div>';

        return implode("\n", $html);
    }

    /**
     *
     * @param string $email
     * @param string $clickable_text
     * @param string $style_class
     * @return string
     */
    public static function encrypted_mailto_link($email, $clickable_text = null, $style_class = '')
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
     * @return string
     */
    public static function header()
    {
        $document_language = Translation :: get_instance()->get_language();
        if (empty($document_language))
        {
            // if there was no valid iso-code, use the english one
            $document_language = 'en';
        }
        $header = Header :: get_instance();
        $header->set_language_code($document_language);
        $header->set_page_title(
            \Chamilo\Configuration\Configuration :: get('Chamilo\Core\Admin', 'institution') . ' - ' .
                 \Chamilo\Configuration\Configuration :: get('Chamilo\Core\Admin', 'site_name'));
        $header->add_default_headers();

        $html = array();

        $html[] = $header->toHtml();
        if (! isset($text_dir))
        {
            $text_dir = 'ltr';
        }
        $html[] = '<body dir="' . $text_dir . '">' . "\n";
        $html[] = '<!-- #outerframe container to control some general layout of all pages -->' . "\n";
        $html[] = '<div id="outerframe">' . "\n";

        // Banner
        $banner = new Banner();
        $html[] = $banner->toHtml();

        $html[] = '<div id="main"> <!-- start of #main wrapper for #content and #menu divs -->';
        $html[] = '<!--   Begin Of script Output   -->';
        $html[] = '<div id="helpbox" class="helpdialog"></div>';

        return implode("\n", $html);
    }

    /**
     *
     * @param string $page_title
     * @param string $style
     * @param string[] $html_headers
     * @return string
     */
    public static function small_header($page_title = null, $style = null, $html_headers = array())
    {
        $page_title = is_null($page_title) ? $page_title = PlatformSetting :: get('site_name') : $page_title;
        $style = is_null($style) ? 'body {background-color:white; padding: 10px;}' : $style;
        $html_headers = is_array($html_headers) ? $html_headers : array($html_headers);
        $document_language = Translation :: get_language();
        if (empty($document_language))
        {
            // if there was no valid iso-code, use the english one
            $document_language = 'en'; // @todo: shouldn't we put that in
                                           // Translation::get_languate()?
        }
        if (! isset($text_dir))
        {
            $text_dir = 'ltr';
        }
        $header = new Header($document_language);
        $header->add_default_headers();
        $header->set_page_title(PlatformSetting :: get('site_name'));
        // @todo: shouldn't we put that somewhere else? or add a class instead?
        if ($style)
        {
            $header->add_html_header('<style type="text/css">' . $style . '</style>');
        }
        $header->add_html_header(
            '<script type="text/javascript">var rootWebPath="' . Path :: getInstance()->getBasePath(true) . '"</script>');
        foreach ($html_headers as $html_header)
        {
            $header->add_html_header($html_header);
        }

        $html = array();

        $html[] = $header->toHtml();
        $html[] = '<body dir="' . $text_dir . '">' . "\n";

        return implode("\n", $html);
    }

    /**
     *
     * @return string
     */
    public static function footer()
    {
        $footer = new Footer();
        return $footer->toHtml();
    }

    /**
     *
     * @return string
     */
    public static function small_footer()
    {
        $html = array();

        $html[] = '</body>' . "\n";
        $html[] = '</html>' . "\n";

        return implode("\n", $html);
    }

    /**
     *
     * @param boolean $show_login_form
     * @return string
     */
    public static function not_allowed($show_login_form = true)
    {
        Session :: register('request_uri', $_SERVER['REQUEST_URI']);

        $html = array();

        $html[] = self :: header();
        $html[] = self :: error_message(Translation :: get('NotAllowed', null, Utilities :: COMMON_LIBRARIES));
        $html[] = self :: footer();

        return implode("\n", $html);
    }

    /**
     *
     * @return string
     */
    public static function maintenance_mode()
    {
        $html = array();

        $html[] = self :: header();
        $html[] = self :: error_message(Translation :: get('MaintenanceModeMessage'));
        $html[] = self :: footer();

        return implode("\n", $html);
    }

    /**
     *
     * @param integer $percent
     * @param integer $step
     * @return string
     */
    public static function get_progress_bar($percent, $step = 2)
    {
        $done = (int) ($percent / $step);
        $rest = (int) (100.0 / $step) - $done;
        return '<div class="progress_information"><div class="progress_bar">' . str_repeat(
            '<div class="done"></div>',
            $done) . str_repeat('<div class=""></div>', $rest) . '</div><div class="progress_status">' . round(
            $percent,
            2) . ' %</div></div>';
    }

    /**
     *
     * @param integer $percent
     * @param string $show_text
     * @param integer $step
     * @return string
     */
    public static function get_rating_bar($percent, $show_text = true, $step = 2)
    {
        $done = (int) ($percent / $step);
        $rest = (int) (100.0 / $step) - $done;
        return '<div class="rating_information"><div class="rating_bar">' .
             str_repeat(
                '<div class="' . ($percent <= 50 ? 'bad' : $percent <= 75 ? 'average' : 'good') . '"></div>',
                $done) . str_repeat('<div class=""></div>', $rest) . '</div>' . ($show_text ? '<div class="rating_status">' . round(
                $percent,
                2) . ' %</div>' : '') . '</div>';
    }

    /**
     *
     * @param string $title
     * @param string $extra_classes
     * @return string
     */
    public static function form_category($title = null, $extra_classes = null)
    {
        $html = array();

        if ($title != null)
        {
            $html[] = '<div class="configuration_form' . ($extra_classes ? ' ' . $extra_classes : '') . '" >';
            $html[] = '<span class="category">' . $title . '</span>';
        }
        else
        {
            $html[] = '<div style="clear: both;"></div>';
            $html[] = '</div>';
        }

        return implode("\n", $html);
    }

    /**
     *
     * @param string $label
     * @param string $value
     * @return string
     */
    public static function form_row($label = null, $value = null)
    {
        $html = array();

        $html[] = '<div class="row">';
        $html[] = '<div class="label">' . $label . '</div>';
        $html[] = '<div class="formw">';
        $html[] = '<div class="element">' . $value . '</div>';
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = '</div>';

        return implode("\n", $html);
    }
}
