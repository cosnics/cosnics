<?php
namespace Chamilo\Core\Repository\Preview;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonDivider;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

abstract class Manager extends Application
{
    // Parameters
    const PARAM_CONTENT_OBJECT_ID = 'preview_content_object_id';
    const PARAM_FORMAT = 'format';
    const PARAM_VIEW = 'view';

    // Available actions
    const ACTION_DISPLAY = 'Display';
    const ACTION_RENDITION = 'Rendition';
    const ACTION_REPORTING = 'Reporting';
    const ACTION_RESET = 'Reset';

    // The Default action
    const DEFAULT_ACTION = self::ACTION_DISPLAY;

    /**
     *
     * @var \core\repository\ContentObject
     */
    private $content_object;

    private $buttonToolBarRenderer;

    /**
     *
     * @param \core\user\storage\data_class\User $user
     * @param \libraries\architecture\application\Application,null $application
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        $content_object_id = Request::get(self::PARAM_CONTENT_OBJECT_ID);
        $this->content_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class_name(),
            $content_object_id);

        if (! $this->content_object instanceof ContentObject)
        {
            throw new NoObjectSelectedException(Translation::get('ContentObject'));
        }

        $this->set_parameter(self::PARAM_CONTENT_OBJECT_ID, $this->content_object->get_id());
    }

    /*
     * (non-PHPdoc) @see \libraries\architecture\application\Application::render_header()
     */
    public function render_header()
    {
        $page = Page::getInstance();
        $page->setViewMode(Page::VIEW_MODE_HEADERLESS);

        $html = array();

        $html[] = $page->getHeader()->toHtml();
        $html[] = $this->renderPreviewHeader();

        return implode(PHP_EOL, $html);
    }

    protected function renderPreviewHeader()
    {
        $html = array();

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12">';
        $html[] = '<div class="alert alert-warning">';
        $html[] = $this->getButtonToolBarRenderer()->render();
        $html[] = $this->getPreviewModeWarning();
        $html[] = '<div class="clearfix"></div>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    protected function getPreviewModeWarning()
    {
        $translation = Translation::get('PreviewModeWarning', null, $this->get_content_object()->package() . '\Display');

        if ($translation == 'PreviewModeWarning')
        {
            $translation = Translation::get('PreviewModeWarning');
        }

        return $translation;
    }

    protected function getButtonToolBarRenderer()
    {
        if (! isset($this->buttonToolBarRenderer))
        {
            $buttonToolBar = new ButtonToolBar(null, array(), array('pull-right'));
            $this->buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);

            $dropdownButton = new DropdownButton(Translation::get('DisplayType'), new FontAwesomeGlyph('th'));
            $dropdownButton->setDropdownClasses('dropdown-menu-right');
            $buttonToolBar->addItem($dropdownButton);

            $isDisplayAction = $this->get_action() == self::ACTION_DISPLAY;
            $previewExists = \Chamilo\Core\Repository\Display\Manager::exists(
                $this->get_content_object()->package() . '\Display\Preview');

            if ($previewExists)
            {
                $classes = ($isDisplayAction ? 'selected' : 'not-selected');

                $dropdownButton->addSubButton(
                    new SubButton(
                        Translation::get('DisplayPreview'),
                        null,
                        $this->get_url(array(self::PARAM_ACTION => self::ACTION_DISPLAY)),
                        SubButton::DISPLAY_ICON_AND_LABEL,
                        false,
                        $classes));
            }

            if ($isDisplayAction && $previewExists && $this->getPreview()->supports_reset())
            {
                $buttonToolBar->addItem(
                    new Button(
                        Translation::get('ResetDisplayPreview'),
                        new FontAwesomeGlyph('repeat'),
                        $this->get_url(array(self::PARAM_ACTION => self::ACTION_RESET)),
                        SubButton::DISPLAY_ICON_AND_LABEL,
                        true));
            }

            $dropdownButton->addSubButton(new SubButtonDivider());

            $views = array(
                ContentObjectRendition::VIEW_FULL,
                ContentObjectRendition::VIEW_PREVIEW,
                ContentObjectRendition::VIEW_THUMBNAIL,
                ContentObjectRendition::VIEW_DESCRIPTION,
                ContentObjectRendition::VIEW_FULL_THUMBNAIL,
                ContentObjectRendition::VIEW_INLINE,
                ContentObjectRendition::VIEW_FORM);

            foreach ($views as $view)
            {
                $isRenditionAction = $this->get_action() == self::ACTION_RENDITION;
                $classes = ($isRenditionAction && $this->getCurrentView() == $view ? 'selected' : 'not-selected');

                $dropdownButton->addSubButton(
                    new SubButton(
                        Translation::get('View' . StringUtilities::getInstance()->createString($view)->upperCamelize()),
                        null,
                        $this->get_url(
                            array(
                                self::PARAM_ACTION => self::ACTION_RENDITION,
                                self::PARAM_FORMAT => $this->getCurrentFormat(),
                                self::PARAM_VIEW => $view)),
                        SubButton::DISPLAY_ICON_AND_LABEL,
                        false,
                        $classes));
            }

            if ($this->has_reporting())
            {
                $dropdownButton->addSubButton(new SubButtonDivider());

                $classes = (self::PARAM_ACTION == self::ACTION_REPORTING ? 'selected' : 'not-selected');

                $dropdownButton->addSubButton(
                    new SubButton(
                        Translation::get('ReportingPreview'),
                        null,
                        $this->get_url(array(self::PARAM_ACTION => self::ACTION_REPORTING)),
                        SubButton::DISPLAY_ICON_AND_LABEL,
                        false,
                        $classes));
            }
        }

        return $this->buttonToolBarRenderer;
    }

    /**
     *
     * @return \core\repository\ContentObject
     */
    public function get_content_object()
    {
        return $this->content_object;
    }

    /**
     *
     * @return boolean
     */
    public function has_reporting()
    {
        return self::reporting($this->get_content_object());
    }

    /**
     *
     * @param \core\repository\ContentObject $content_object
     *
     * @return boolean
     */
    public static function reporting($content_object)
    {
        $package = $content_object->package();
        $reporting_manager_class = $package . '\Integration\Chamilo\Core\Reporting\Preview\Manager';

        return class_exists($reporting_manager_class);
    }

    /**
     *
     * @param \core\repository\ContentObject,int $content_object
     */
    public static function get_content_object_default_action_link($contentObject)
    {
        if (! $contentObject instanceof ContentObject && is_numeric($contentObject))
        {
            $contentObject = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(),
                $contentObject);
        }

        if (! $contentObject instanceof ContentObject)
        {
            throw new NoObjectSelectedException(Translation::get('ContentObject'));
        }

        if (\Chamilo\Core\Repository\Display\Manager::exists($contentObject->package() . '\Display\Preview'))
        {
            $action = self::ACTION_DISPLAY;
        }
        else
        {
            $action = self::ACTION_RENDITION;
        }

        $redirect = new Redirect(
            array(
                self::PARAM_CONTEXT => self::context(),
                self::PARAM_ACTION => $action,
                self::PARAM_CONTENT_OBJECT_ID => $contentObject->get_id()));

        return $redirect->getUrl();
    }

    public function getPreview()
    {
        $package = $this->get_content_object()->package();
        $context = $package . '\Display\Preview';

        return $this->getApplicationFactory()->getApplication(
            $context,
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
    }

    public function getCurrentView()
    {
        return $this->getRequest()->query->get(self::PARAM_VIEW, ContentObjectRendition::VIEW_FULL);
    }

    public function getCurrentFormat()
    {
        return $this->getRequest()->query->get(self::PARAM_FORMAT, ContentObjectRendition::FORMAT_HTML);
    }
}
