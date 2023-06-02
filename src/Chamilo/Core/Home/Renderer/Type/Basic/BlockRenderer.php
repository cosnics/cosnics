<?php
namespace Chamilo\Core\Home\Renderer\Type\Basic;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Home\Architecture\ConfigurableInterface;
use Chamilo\Core\Home\Interfaces\StaticBlockTitleInterface;
use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Rights\Form\ElementTargetEntitiesForm;
use Chamilo\Core\Home\Rights\Service\ElementRightsService;
use Chamilo\Core\Home\Rights\Storage\Repository\RightsRepository;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package common.libraries
 */
class BlockRenderer
{
    use DependencyInjectionContainerTrait;

    public const BLOCK_PROPERTY_ID = 'id';
    public const BLOCK_PROPERTY_IMAGE = 'image';
    public const BLOCK_PROPERTY_NAME = 'name';

    public const PARAM_ACTION = 'block_action';

    public const SOURCE_AJAX = 2;
    public const SOURCE_DEFAULT = 1;

    /**
     * Caching variable for general mode
     *
     * @var bool
     */
    protected $generalMode;

    /**
     * The source from which this block renderer is called
     *
     * @var int
     */
    protected $source;

    /**
     * @var \Chamilo\Core\Home\Storage\DataClass\Block
     */
    private $block;

    /**
     * @var \Chamilo\Core\Home\Service\HomeService
     */
    private $homeService;

    /**
     * @var \Chamilo\Core\Home\Renderer\Renderer
     */
    private $renderer;

    private $type;

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param \Chamilo\Core\Home\Service\HomeService $homeService
     * @param \Chamilo\Core\Home\Storage\DataClass\Block $block
     * @param int $source
     */
    public function __construct(
        Application $application, HomeService $homeService, Block $block, $source = self::SOURCE_DEFAULT
    )
    {
        $this->renderer = $application;
        $this->homeService = $homeService;
        $this->block = $block;
        $this->source = $source;

        $this->initializeContainer();
    }

    public function displayActions()
    {
        $html = [];

        $userHomeAllowed = Configuration::getInstance()->get_setting([Manager::CONTEXT, 'allow_user_home']);
        $generalMode = $this->isInGeneralMode();
        $isIdentifiedUser = $this->getUser() && !$this->getUser()->is_anonymous_user();

        if ($this->getUser() instanceof User && ($userHomeAllowed || $generalMode) && $isIdentifiedUser)
        {
            if ($this->isHidable())
            {
                $glyphVisible = new FontAwesomeGlyph('chevron-down');
                $textVisible = Translation::get('ShowBlock');

                $html[] = '<a href="#" class="portal-action portal-action-block-show' .
                    (!$this->getBlock()->isVisible() ? '' : ' hidden') . '" title="' . $textVisible . '">' .
                    $glyphVisible->render() . '</a>';

                $glyphVisible = new FontAwesomeGlyph('chevron-up');
                $textVisible = Translation::get('HideBlock');

                $html[] = '<a href="#" class="portal-action portal-action-block-hide' .
                    (!$this->getBlock()->isVisible() ? ' hidden' : '') . '" title="' . $textVisible . '">' .
                    $glyphVisible->render() . '</a>';
            }

            if ($generalMode)
            {
                $glyph = new FontAwesomeGlyph('user');
                $configure_text = Translation::get('SelectTargetUsersGroups');

                $html[] = '<a href="#" class="portal-action portal-action-block-configure-target-entities" title="' .
                    $configure_text . '">' . $glyph->render() . '</a>';
            }

            if ($this->isConfigurable())
            {
                $glyph = new FontAwesomeGlyph('wrench');
                $configure_text = Translation::get('Configure');

                $html[] =
                    '<a href="#" class="portal-action portal-action-block-configure" title="' . $configure_text . '">' .
                    $glyph->render() . '</a>';
            }

            if ($this->isDeletable())
            {
                $glyph = new FontAwesomeGlyph('times');
                $delete_text = Translation::get('Delete');

                $html[] = '<a href="#" class="portal-action portal-action-block-delete" title="' . $delete_text . '">' .
                    $glyph->render() . '</a>';
            }
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string
     */
    public function displayContent()
    {
        return '';
    }

    public function displayTitle()
    {
        $html = [];

        $html[] =
            '<div class="panel-heading' . ($this->getBlock()->isVisible() ? '' : ' panel-heading-without-content') .
            '">';
        $html[] = '<div class="pull-right">' . $this->displayActions() . '</div>';
        $html[] = '<h3 class="panel-title">' . $this->getTitle() . '</h3>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return \Chamilo\Core\Home\Storage\DataClass\Block
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * @return \Chamilo\Core\Home\Service\HomeService
     */
    public function getHomeService()
    {
        return $this->homeService;
    }

    public function getLink($parameters = [])
    {
        return $this->getUrlGenerator()->fromParameters($parameters);
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
     * @return \Chamilo\Core\Home\Renderer\Renderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * @return int
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Returns the block's title to display.
     *
     * @return string
     */
    public function getTitle()
    {
        return htmlspecialchars($this->getBlock()->getTitle());
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

    public function getUrl($parameters = [], $filter = [], $encode_entities = false)
    {
        return $this->getRenderer()->get_url($parameters, $filter, $encode_entities);
    }

    public function getUser()
    {
        return $this->getRenderer()->get_user();
    }

    /**
     * @see Tool::get_user_id()
     */
    public function getUserId()
    {
        return $this->getRenderer()->get_user_id();
    }

    /**
     * @param string[] $parameters
     * @param bool $encode
     *
     * @return string
     * @deprecated User getLink() now
     */
    public function get_link($parameters = [], $encode = false)
    {
        return $this->getLink($parameters, $encode);
    }

    public function get_parameter($name)
    {
        return $this->getRenderer()->get_parameter($name);
    }

    public function get_parameters()
    {
        return $this->getRenderer()->get_parameters();
    }

    /**
     * @return bool
     */
    public function hasStaticTitle()
    {
        return $this instanceof StaticBlockTitleInterface;
    }

    /**
     * @return bool
     */
    public function isConfigurable()
    {
        return $this instanceof ConfigurableInterface;
    }

    /**
     * @return bool
     */
    public function isDeletable()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isEditable()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidable()
    {
        return true;
    }

    /**
     * @return bool
     */
    protected function isInGeneralMode()
    {
        if (!isset($this->generalMode))
        {
            $this->generalMode =
                $this->getUser() && $this->getUser()->is_platform_admin() && $this->getHomeService()->isInGeneralMode();
        }

        return $this->generalMode;
    }

    /**
     * Default response for blocks who use an attachment viewer.
     * Override for different functionality.
     *
     * @param ContentObject $object The content object to be tested.
     *
     * @return bool default response: false.
     */
    public function isViewAttachmentAllowed($object)
    {
        return false;
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
        return $this->getSession()->get(\Chamilo\Core\User\Manager::SESSION_USER_IO) != 0;
    }

    /**
     * @return string
     */
    public function renderContentFooter()
    {
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string
     */
    public function renderContentHeader()
    {
        $html = [];

        $html[] = '<div class="portal-block-content' . ($this->getBlock()->isVisible() ? '' : ' hidden') . '">';
        $html[] = '<div class="panel-body">';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string
     */
    public function renderFooter()
    {
        $html = [];

        $html[] = $this->renderContentFooter();
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function renderHeader()
    {
        $html = [];
        $html[] = '<div class="panel panel-default portal-block" data-column-id="' . $this->getBlock()->getParentId() .
            '" data-element-id="' . $this->getBlock()->get_id() . '">';
        $html[] = $this->displayTitle();

        if ($this->isConfigurable())
        {
            $html[] = '<div class="portal-block-form hidden">';
            $html[] = '<div class="panel-body">';

            $formClassName = $this->getBlock()->getContext() . '\Integration\Chamilo\Core\Home\Form\\' .
                $this->getBlock()->getBlockType() . 'Form';

            if (class_exists($formClassName))
            {
                $form = new $formClassName($this->getBlock(), $this->hasStaticTitle());
                $html[] = $form->toHtml();
            }

            $html[] = '</div>';
            $html[] = '</div>';
        }

        if ($this->isInGeneralMode())
        {
            $html[] = '<div class="portal-block-target-entities-form hidden">';
            $html[] = '<div class="panel-body">';

            $form = new ElementTargetEntitiesForm(
                $this->getBlock(), $this->getUrl(), new ElementRightsService(new RightsRepository())
            );

            $html[] = $form->toHtml();

            $html[] = '</div>';
            $html[] = '</div>';
        }

        $html[] = $this->renderContentHeader();

        $html[] = '<div style="overflow:auto;">';

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Core\Home\Service\HomeService $homeService
     */
    public function setHomeService(HomeService $homeService)
    {
        $this->homeService = $homeService;
    }

    public function toHtml($view = '')
    {
        if (!$this->isVisible())
        {
            return '';
        }

        $html = [];
        $html[] = $this->renderHeader();
        $html[] = $this->displayContent();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }
}
