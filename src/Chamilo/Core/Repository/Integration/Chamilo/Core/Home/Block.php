<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Home;

use Chamilo\Core\Home\Renderer\BlockRenderer;
use Chamilo\Core\Home\Repository\ContentObjectPublicationRepository;
use Chamilo\Core\Home\Service\ContentObjectPublicationService;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Publication\Storage\Repository\PublicationRepository;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Translation\Translation;

/**
 * Base class for blocks based on a content object.
 *
 * @copyright (c) 2011 University of Geneva
 * @license       GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author        lopprecht
 */
class Block extends BlockRenderer
{
    public const CONFIGURATION_OBJECT_ID = 'use_object';

    /**
     * @var ContentObjectPublication
     */
    protected $contentObjectPublication;

    /**
     * @var ContentObjectPublicationService
     */
    protected $contentObjectPublicationService;

    protected $defaultTitle = '';

    private $template_callback_context = [];

    public function __construct(HomeService $homeService, $source = self::SOURCE_DEFAULT, $defaultTitle = '')
    {
        parent::__construct($homeService, $source);
        $this->defaultTitle = $defaultTitle ?: Translation::get('Object');

        $this->contentObjectPublicationService = new ContentObjectPublicationService(
            new ContentObjectPublicationRepository(new PublicationRepository())
        );
    }

    /**
     * Returns the html to display when the block is configured.
     *
     * @return string
     */
    public function displayContent()
    {
        $content_object = $this->getObject();

        return ContentObjectRenditionImplementation::launch(
            $content_object, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_DESCRIPTION, $this
        );
    }

    /**
     * Returns the html to display when the block is not configured.
     *
     * @return string
     */
    public function displayEmpty()
    {
        return Translation::get('ConfigureBlockFirst', null, \Chamilo\Core\Home\Manager::CONTEXT);
    }

    /**
     * @see \Chamilo\Core\Home\Architecture\ConfigurableInterface::getConfigurationVariables()
     */
    public function getConfigurationVariables()
    {
        return [];
    }

    /**
     * Returns an array of the configuration values that return content object ids that need to be published in the
     * home application
     *
     * @return string[]
     */
    public function getContentObjectConfigurationVariables()
    {
        return [self::CONFIGURATION_OBJECT_ID];
    }

    /**
     * Returns the content object publication for this block
     */
    protected function getContentObjectPublication()
    {
        if (!isset($this->contentObjectPublication))
        {
            $this->contentObjectPublication =
                $this->contentObjectPublicationService->getFirstContentObjectPublicationForElement(
                    $this->getBlock()
                );
        }

        return $this->contentObjectPublication;
    }

    /**
     * The default's title value.
     * That is the title to display when the block is not linked to a content object.
     *
     * @return string
     */
    protected function getDefaultTitle()
    {
        return $this->defaultTitle;
    }

    /**
     * If the block is linked to an object returns it.
     * Otherwise returns null.
     *
     * @return ContentObject
     */
    public function getObject()
    {
        $contentObjectPublication = $this->getContentObjectPublication();

        if ($contentObjectPublication instanceof ContentObjectPublication)
        {
            return $contentObjectPublication->getContentObject();
        }

        return null;
    }

    /**
     * If the block is linked to an object returns the object id.
     * Otherwise returns 0.
     *
     * @return int
     */
    public function getObjectId()
    {
        $contentObjectPublication = $this->getContentObjectPublication();

        if ($contentObjectPublication instanceof ContentObjectPublication)
        {
            return $contentObjectPublication->get_content_object_id();
        }

        return null;
    }

    /**
     * Returns the text title to display.
     * That is the content's object title if the block is configured or the default
     * title otherwise;
     *
     * @return string
     */
    public function getTitle()
    {
        $content_object = $this->getObject();

        return empty($content_object) ? $this->getDefaultTitle() : $content_object->get_title();
    }

    /**
     * Constructs the attachment url for the given attachment and the current object.
     *
     * @param ContentObject $attachment The attachment for which the url is needed.
     *
     * @return mixed the url, or null if no view right.
     */
    public function get_content_object_display_attachment_url($attachment)
    {
        if (!$this->isViewAttachmentAllowed($this->getObject()))
        {
            return null;
        }

        return $this->getUrl(
            [
                \Chamilo\Core\Home\Manager::PARAM_CONTEXT => \Chamilo\Core\Home\Manager::CONTEXT,
                \Chamilo\Core\Home\Manager::PARAM_ACTION => \Chamilo\Core\Home\Manager::ACTION_VIEW_ATTACHMENT,
                \Chamilo\Core\Home\Manager::PARAM_PARENT_ID => $this->getObject()->get_id(),
                \Chamilo\Core\Home\Manager::PARAM_OBJECT_ID => $attachment->get_id()
            ]
        );
    }

    // BASIC TEMPLATING FUNCTIONS.

    // @TODO: remove that when we move to a templating system
    // @NOTE: could be more efficient to do an include or eval

    /**
     * Return true if the block is linked to an object.
     * Otherwise returns false.
     *
     * @return bool
     */
    public function isConfigured()
    {
        return $this->getObjectId() != 0;
    }

    protected function process_template($template, $vars)
    {
        $pattern = '/\{\$[a-zA-Z_][a-zA-Z0-9_]*\}/';
        $this->template_callback_context = $vars;
        $template = preg_replace_callback($pattern, [$this, 'process_template_callback'], $template);

        return $template;
    }

    private function process_template_callback($matches)
    {
        $vars = $this->template_callback_context;
        $name = trim($matches[0], '{$}');
        $result = isset($vars[$name]) ? $vars[$name] : '';

        return $result;
    }

    protected function setDefaultTitle($value)
    {
        $this->defaultTitle = $value;
    }

    public function toHtml($view = '')
    {
        if (!$this->isVisible())
        {
            return '';
        }

        $html = [];
        $html[] = $this->renderHeader();
        $html[] = $this->isConfigured() ? $this->displayContent() : $this->displayEmpty();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }
}
