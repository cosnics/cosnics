<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceUtilities;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 * $Id: header.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 *
 * @package common.html
 */
/**
 * Class to display the header of a HTML-page
 */
class Header
{

    /**
     * Singleton
     *
     * @var Header
     */
    private static $instance;

    /**
     * The http headers which will be send to the browser using php's header (...) function.
     */
    private $http_headers;

    /**
     * The html headers which will be added in the <head> tag of the html document.
     */
    private $html_headers;

    /**
     * The language code
     */
    private $language_code;

    /**
     * Constructor
     */
    public function __construct($language_code = 'en')
    {
        $this->http_headers = array();
        $this->html_headers = array();
        $this->set_language_code($language_code);
    }

    /**
     *
     * @return Header
     */
    public static function get_instance()
    {
        if (self :: $instance == null)
        {
            self :: set_instance(new Header());
        }

        return self :: $instance;
    }

    public static function set_instance($instance)
    {
        self :: $instance = $instance;
    }

    public function set_language_code($language_code)
    {
        $this->language_code = $language_code;
    }

    /**
     * Adds some default headers to the output
     */
    public function add_default_headers()
    {
        $server_type = \Chamilo\Configuration\Configuration :: get('Chamilo\Core\Admin', 'server_type');
        $theme = Theme :: getInstance()->getTheme();

        $parameters = array();
        $parameters[Application :: PARAM_CONTEXT] = 'Chamilo\Libraries\Ajax';
        $parameters[Application :: PARAM_ACTION] = 'resource';
        $parameters[ResourceUtilities :: PARAM_THEME] = $theme;
        $parameters[ResourceUtilities :: PARAM_SERVER_TYPE] = $server_type;

        $this->add_http_header('Content-Type: text/html; charset=UTF-8');
        $this->add_http_header(
            'X-Powered-By: Chamilo ' . \Chamilo\Configuration\Configuration :: get('Chamilo\Core\Admin', 'version'));

        $this->add_html_header('<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />');

        $parameters[ResourceUtilities :: PARAM_TYPE] = 'css';
        $context = http_build_query($parameters);

        $this->add_css_file_header(Path :: getInstance()->getBasePath(true) . 'index.php?' . $context);

        $this->add_link_header(Path :: getInstance()->getBasePath(true) . 'index.php', 'top');
        $this->add_link_header('http://help.chamilo.org/', 'help');
        $this->add_link_header(
            Theme :: getInstance()->getCommonImagePath('Favicon', 'ico'),
            'shortcut icon',
            null,
            'image/x-icon');
        $this->add_html_header(
            '<script type="text/javascript">var rootWebPath="' . Path :: getInstance()->getBasePath(true) . '";</script>');

        $parameters[ResourceUtilities :: PARAM_TYPE] = 'javascript';
        $context = http_build_query($parameters);
        $this->add_javascript_file_header(Path :: getInstance()->getBasePath(true) . 'index.php?' . $context);
    }

    /**
     * Adds a http header
     */
    public function add_http_header($http_header)
    {
        $this->http_headers[] = $http_header;
    }

    /**
     * Adds a html header
     */
    public function add_html_header($html_header)
    {
        $this->html_headers[] = $html_header;
    }

    /**
     * Sets the page title
     */
    public function set_page_title($title)
    {
        $this->add_html_header('<title>' . $title . '</title>');
    }

    /**
     * Adds a css file
     */
    public function add_css_file_header($file, $media = 'screen,projection')
    {
        $header = '<link rel="stylesheet" type="text/css" media="' . $media . '" href="' . $file . '" />';
        $this->add_html_header($header);
    }

    /**
     * Adds a javascript file
     */
    public function add_javascript_file_header($file)
    {
        $header[] = '<script type="text/javascript" src="' . $file . '"></script>';
        $this->add_html_header(implode(' ', $header));
    }

    /**
     * Adds a link
     */
    public function add_link_header($url, $rel = null, $title = null, $type = null)
    {
        $type = $type ? ' type="' . $type . '"' : '';
        $title = $title ? ' title="' . $title . '"' : '';
        $rel = $rel ? ' rel="' . $rel . '"' : '';
        $href = ' href="' . $url . '"';
        $header = '<link' . $href . $rel . $title . $type . '/>';
        $this->add_html_header($header);
    }

    /**
     * Creates the HTML output for the header. This function will send all http headers to the browser and return the
     * head-tag of the html document
     */
    public function toHtml()
    {
        foreach ($this->http_headers as $index => & $http_header)
        {
            header($http_header);
        }
        $output[] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        $output[] = '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . $this->language_code . '" lang="' .
             $this->language_code . '">';
        $output[] = ' <head>';
        foreach ($this->html_headers as $index => & $html_header)
        {
            $output[] = '  ' . $html_header;
        }
        $output[] = ' </head>';
        return implode(PHP_EOL, $output);
    }

    public function get_section()
    {
        return $this->section;
    }

    public function set_section($section)
    {
        $this->section = $section;
    }
}
