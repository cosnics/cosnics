<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Home;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * Base class for blocks based on a content object.
 *
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author lopprecht
 */
class Block extends \Chamilo\Core\Home\BlockRendition
{

    protected $default_title = '';

    public function __construct($parent, $block_info, $configuration, $default_title = '')
    {
        parent :: __construct($parent, $block_info, $configuration);
        $this->default_title = $default_title ? $default_title : Translation :: get('Object');
    }

    /**
     * The default's title value.
     * That is the title to display when the block is not linked to a content object.
     *
     * @return string
     */
    protected function get_default_title()
    {
        return $this->default_title;
    }

    protected function set_default_title($value)
    {
        $this->default_title = $value;
    }

    /**
     * If the block is linked to an object returns the object id.
     * Otherwise returns 0.
     *
     * @return int
     */
    public function get_object_id()
    {
        return $this->get('use_object', 0);
    }

    /**
     * Return configuration property.
     *
     * @param string $name Name of the configuration property to retrieve
     * @param object $default Default value to return if property is not defined
     * @return object Configuration property value.
     */
    public function get($name, $default = null)
    {
        $configuration = $this->get_configuration();

        $result = isset($configuration[$name]) ? $configuration[$name] : null;
        return $result;
    }

    /**
     * If the block is linked to an object returns it.
     * Otherwise returns null.
     *
     * @return ContentObject
     */
    public function get_object()
    {
        $object_id = $this->get_object_id();
        if ($object_id == 0)
        {
            return null;
        }
        else
        {
            return \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                ContentObject :: class_name(),
                $object_id);
        }
    }

    /**
     * Return true if the block is linked to an object.
     * Otherwise returns false.
     *
     * @return bool
     */
    public function is_configured()
    {
        $object_id = $this->get_object_id();
        return $object_id != 0;
    }

    public function as_html($view = '')
    {
        if (! $this->is_visible())
        {
            return '';
        }
        if ($view)
        {
            $this->set_view($view);
        }

        $html = array();
        $html[] = $this->render_header();
        $html[] = $this->is_configured() ? $this->display_content() : $this->display_empty();
        $html[] = $this->render_footer();
        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the html to display when the block is not configured.
     *
     * @return string
     */
    public function display_empty()
    {
        return Translation :: get('ConfigureBlockFirst', null, \Chamilo\Core\Home\Manager :: context());
    }

    /**
     * Returns the html to display when the block is configured.
     *
     * @return string
     */
    public function display_content()
    {
        $content_object = $this->get_object();

        return ContentObjectRenditionImplementation :: launch(
            $content_object,
            ContentObjectRendition :: FORMAT_HTML,
            ContentObjectRendition :: VIEW_DESCRIPTION,
            $this);
    }

    /**
     * Returns the text title to display.
     * That is the content's object title if the block is configured or the default
     * title otherwise;
     *
     * @return string
     */
    public function get_title()
    {
        $content_object = $this->get_object();
        return empty($content_object) ? $this->get_default_title() : $content_object->get_title();
    }

    // BASIC TEMPLATING FUNCTIONS.

    // @TODO: remove that when we move to a templating system
    // @NOTE: could be more efficient to do an include or eval
    private $template_callback_context = array();

    protected function process_template($template, $vars)
    {
        $pattern = '/\{\$[a-zA-Z_][a-zA-Z0-9_]*\}/';
        $this->template_callback_context = $vars;
        $template = preg_replace_callback($pattern, array($this, 'process_template_callback'), $template);
        return $template;
    }

    private function process_template_callback($matches)
    {
        $vars = $this->template_callback_context;
        $name = trim($matches[0], '{$}');
        $result = isset($vars[$name]) ? $vars[$name] : '';
        return $result;
    }

    /**
     * Constructs the attachment url for the given attachment and the current object.
     *
     * @param ContentObject $attachment The attachment for which the url is needed.
     * @return mixed the url, or null if no view right.
     */
    public function get_content_object_display_attachment_url($attachment)
    {
        if (! $this->is_view_attachment_allowed($this->get_object()))
        {
            return null;
        }
        return $this->get_url(
            array(
                \Chamilo\Core\Home\Manager :: PARAM_CONTEXT => \Chamilo\Core\Home\Manager :: context(),
                \Chamilo\Core\Home\Manager :: PARAM_ACTION => \Chamilo\Core\Home\Manager :: ACTION_VIEW_ATTACHMENT,
                \Chamilo\Core\Home\Manager :: PARAM_PARENT_ID => $this->get_object()->get_id(),
                \Chamilo\Core\Home\Manager :: PARAM_OBJECT_ID => $attachment->get_id()));
    }
}
