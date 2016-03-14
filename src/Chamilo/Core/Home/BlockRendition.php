<?php
namespace Chamilo\Core\Home;

use Chamilo\Core\Home\Architecture\ConfigurableInterface;
use Chamilo\Core\Home\Renderer\Renderer;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Format\Structure\ActionBar\BootstrapGlyph;

/**
 *
 * @package common.libraries
 */
class BlockRendition
{
    const PARAM_ACTION = 'block_action';
    const BLOCK_LIST_SIMPLE = 'simple';
    const BLOCK_LIST_ADVANCED = 'advanced';
    const BLOCK_PROPERTY_ID = 'id';
    const BLOCK_PROPERTY_NAME = 'name';
    const BLOCK_PROPERTY_IMAGE = 'image';

    /**
     *
     * @var \Chamilo\Core\Home\Renderer\Renderer
     */
    private $renderer;

    private $type;

    /**
     *
     * @var \Chamilo\Core\Home\Storage\DataClass\Block
     */
    private $block;

    private $configuration;

    /**
     *
     * @param \Chamilo\Core\Home\Renderer\Renderer $renderer
     * @param \Chamilo\Core\Home\Storage\DataClass\Block $block
     * @return \Chamilo\Core\Home\BlockRendition
     */
    public static function factory(Renderer $renderer, Block $block)
    {
        $class = $block->getContext() . '\Integration\Chamilo\Core\Home\Type\\' . $block->getBlockType();
        return new $class($renderer, $block);
    }

    /**
     * Returns the default image to be displayed for block's creation.
     * Can be redefined in subclasses to change the
     * default icon.
     *
     * @todo : not so good. would be better to make the whole "create block" a view.
     * @param string $application
     * @param string $type
     * @param string $size
     * @return string
     */
    public static function getImagePath($context, $type)
    {
        $class = $context . '\Type\\' . StringUtilities :: getInstance()->createString($type)->upperCamelize();

        if (method_exists($class, 'getDefaultImagePath'))
        {
            return $class :: getDefaultImagePath();
        }
        else
        {
            $imagePath = Theme :: getInstance()->getImagePath($context, 'Blocks/' . $type, 'png', false);

            if (! file_exists($imagePath) || ! is_file($imagePath))
            {
                return Theme :: getInstance()->getImagePath(
                    ClassnameUtilities :: getInstance()->getNamespaceParent($context, 4),
                    'Logo/' . Theme :: ICON_MEDIUM);
            }
            else
            {
                return Theme :: getInstance()->getImagePath($context, 'Blocks/' . $type);
            }
        }
    }

    /**
     *
     * @param \Chamilo\Core\Home\Renderer\Renderer $renderer
     * @param \Chamilo\Core\Home\Storage\DataClass\Block $block
     */
    public function __construct($renderer, $block)
    {
        $this->renderer = $renderer;
        $this->block = $block;
    }

    /**
     *
     * @return \Chamilo\Core\Home\Renderer\Renderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     *
     * @see Tool::get_user_id()
     */
    public function getUserId()
    {
        return $this->getRenderer()->get_user_id();
    }

    public function getUser()
    {
        return $this->getRenderer()->get_user();
    }

    /**
     * Returns the types of content object that this object may publish
     *
     * @return array The types.
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     *
     * @return \Chamilo\Core\Home\Storage\DataClass\Block
     */
    public function getBlock()
    {
        return $this->block;
    }

    public function isEditable()
    {
        return true;
    }

    public function isHidable()
    {
        return true;
    }

    public function isDeletable()
    {
        return true;
    }

    public function isConfigurable()
    {
        return $this instanceof ConfigurableInterface;
    }

    /**
     * Returns true if the block is to be displayed, false otherwise.
     * By default do not show on home page when user is
     * not connected.
     *
     * @return bool
     */
    public function isVisible()
    {
        return Session :: get_user_id() != 0;
    }

    /**
     * Returns the block's title to display.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getBlock()->getTitle();
    }

    /**
     * Link target for external links.
     * I.e. links that do not modify the widget itself. In widget mode they should point
     * to a new windows.
     */
    public function getLinkTarget()
    {
        return '';
    }

    /**
     * Returns the url to the icon.
     *
     * @return string
     */
    public function getIcon()
    {
        return Theme :: getInstance()->getImagePath($this->getBlock()->getContext(), 'Logo/' . Theme :: ICON_MINI);
    }

    public function toHtml($view = '')
    {
        if (! $this->isVisible())
        {
            return '';
        }

        $html = array();
        $html[] = $this->renderHeader();
        $html[] = $this->displayContent();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function renderHeader()
    {
        $html = array();
        $html[] = '<div class="panel panel-default portal-block" data-column-id="' . $this->getBlock()->getParentId() .
             '" data-element-id="' . $this->getBlock()->get_id() . '">';
        $html[] = $this->displayTitle();

        if ($this->isConfigurable())
        {
            $html[] = '<div class="portal-block-form hidden">';
            $html[] = '<div class="panel-body">';
            $html[] = '</div>';
            $html[] = '</div>';
        }

        $html[] = '<div class="portal-block-content' . ($this->getBlock()->isVisible() ? '' : ' hidden') . '">';
        $html[] = '<div class="panel-body">';

        return implode(PHP_EOL, $html);
    }

    public function displayTitle()
    {
        $html = array();

        $html[] = '<div class="panel-heading' . ($this->getBlock()->isVisible() ? '' : ' panel-heading-without-content') .
             '">';
        $html[] = '<div class="pull-right">' . $this->displayActions() . '</div>';
        $html[] = '<h3 class="panel-title">' . htmlspecialchars($this->getTitle()) . '</h3>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function displayActions()
    {
        $html = array();

        $userHomeAllowed = PlatformSetting :: get('allow_user_home', Manager :: context());

        if ($this->getUser() instanceof User && ($userHomeAllowed || $this->getUser()->is_platform_admin()) &&
             ! $this->getUser()->is_anonymous_user())
        {
            if ($this->isHidable())
            {
                $glyphVisible = new BootstrapGlyph('chevron-down');
                $textVisible = Translation :: get('ShowBlock');

                $html[] = '<a href="#" class="portal-action portal-action-block-show' .
                     (! $this->getBlock()->isVisible() ? '' : ' hidden') . '" title="' . $textVisible . '">' .
                     $glyphVisible->render() . '</a>';

                $glyphVisible = new BootstrapGlyph('chevron-up');
                $textVisible = Translation :: get('HideBlock');

                $html[] = '<a href="#" class="portal-action portal-action-block-hide' .
                     (! $this->getBlock()->isVisible() ? ' hidden' : '') . '" title="' . $textVisible . '">' .
                     $glyphVisible->render() . '</a>';
            }

            if ($this->isConfigurable())
            {
                $glyph = new BootstrapGlyph('wrench');
                $configure_text = Translation :: get('Configure');

                $html[] = '<a href="#" class="portal-action portal-action-block-configure" title="' . $configure_text .
                     '">' . $glyph->render() . '</a>';
            }

            if ($this->isDeletable())
            {
                $glyph = new BootstrapGlyph('remove');
                $delete_text = Translation :: get('Delete');

                $html[] = '<a href="#" class="portal-action portal-action-block-delete" title="' . $delete_text . '">' .
                     $glyph->render() . '</a>';
            }
        }

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function displayContent()
    {
        return '';
    }

    /**
     *
     * @return string
     */
    public function renderFooter()
    {
        $html = array();

        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param Block $block
     * @return string
     */
    public function getBlockVisibilityLink(Block $block)
    {
        return $this->getManipulationLink(
            array(
                Manager :: PARAM_ACTION => Manager :: ACTION_EDIT_HOME,
                Manager :: PARAM_HOME_TYPE => Manager :: TYPE_BLOCK,
                Manager :: PARAM_HOME_ID => $block->get_id()));
    }

    public function getBlockDeletingLink($home_block)
    {
        return '#';
    }

    public function getBlockEditingLink($home_block)
    {
        return $this->getManipulationLink(
            array(
                Manager :: PARAM_ACTION => Manager :: ACTION_EDIT_HOME_PERSONAL,
                Manager :: PARAM_HOME_TYPE => Manager :: TYPE_BLOCK,
                Manager :: PARAM_HOME_ID => $home_block->get_id()));
    }

    public function getBlockConfiguringLink($home_block)
    {
        return $this->getManipulationLink(
            array(
                Manager :: PARAM_ACTION => Manager :: ACTION_CONFIGURE_HOME_PERSONAL,
                Manager :: PARAM_HOME_TYPE => Manager :: TYPE_BLOCK,
                Manager :: PARAM_HOME_ID => $home_block->get_id()));
    }

    public function getManipulationLink($parameters)
    {
        $redirect = new Redirect($parameters);
        return $redirect->getUrl();
    }

    /**
     *
     * @param string[] $parameters
     * @param boolean $encode
     * @return string
     * @deprecated User getLink() now
     */
    public function get_link($parameters = array(), $encode = false)
    {
        return $this->getLink($parameters, $encode);
    }

    public function getLink($parameters = array(), $encode = false)
    {
        $redirect = new Redirect($parameters, array(), $encode);
        return $redirect->getUrl();
    }

    public function getUrl($parameters = array(), $filter = array(), $encode_entities = false)
    {
        if ($widget_id = Request :: get(Renderer :: PARAM_WIDGET_ID))
        {
            $parameters[Renderer :: PARAM_WIDGET_ID] = $widget_id;
        }
        $result = $this->getRenderer()->get_url($parameters, $filter, $encode_entities);
        return $result;
    }

    public function get_parameter($name)
    {
        return $this->getRenderer()->get_parameter($name);
    }

    public function get_parameters()
    {
        $result = $this->getRenderer()->get_parameters();
        if ($widget_id = Request :: get(Renderer :: PARAM_WIDGET_ID))
        {
            $result[Renderer :: PARAM_WIDGET_ID] = $widget_id;
        }

        return $result;
    }

    /**
     * Default response for blocks who use an attachment viewer.
     * Override for different functionality.
     *
     * @param ContentObject $object The content object to be tested.
     * @return boolean default response: false.
     */
    public function isViewAttachmentAllowed($object)
    {
        return false;
    }
}
