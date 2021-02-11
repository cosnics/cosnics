<?php
namespace Chamilo\Core\Repository\Preview;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonDivider;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

abstract class Manager extends Application
{
    const ACTION_DISPLAY = 'Display';
    const ACTION_RENDITION = 'Rendition';
    const ACTION_RESET = 'Reset';

    const DEFAULT_ACTION = self::ACTION_DISPLAY;

    const PARAM_CONTENT_OBJECT_ID = 'preview_content_object_id';
    const PARAM_FORMAT = 'format';
    const PARAM_VIEW = 'view';

    /**
     *
     * @var \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    private $content_object;

    /**
     * @var \Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer
     */
    private $buttonToolBarRenderer;

    /**
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        $content_object_id = $this->getRequest()->query->get(self::PARAM_CONTENT_OBJECT_ID);
        $this->content_object = DataManager::retrieve_by_id(
            ContentObject::class, $content_object_id
        );

        if (!$this->content_object instanceof ContentObject)
        {
            throw new NoObjectSelectedException(Translation::get('ContentObject'));
        }

        $repositoryRightsService = RightsService::getInstance();

        if (!$repositoryRightsService->hasContentObjectOwnerRights($this->get_user(), $this->get_content_object()))
        {
            throw new NotAllowedException();
        }

        $this->set_parameter(self::PARAM_CONTENT_OBJECT_ID, $this->content_object->getId());
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException
     * @throws \ReflectionException
     */
    protected function getButtonToolBarRenderer()
    {
        if (!isset($this->buttonToolBarRenderer))
        {
            $buttonToolBar = new ButtonToolBar(null, array(), array('pull-right'));
            $this->buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);

            $dropdownButton = new DropdownButton(Translation::get('DisplayType'), new FontAwesomeGlyph('th'));
            $dropdownButton->setDropdownClasses('dropdown-menu-right');
            $buttonToolBar->addItem($dropdownButton);

            $isDisplayAction = $this->get_action() == self::ACTION_DISPLAY;
            $previewExists = \Chamilo\Core\Repository\Display\Manager::exists(
                $this->get_content_object()->package() . '\Display\Preview'
            );

            if ($previewExists)
            {
                $dropdownButton->addSubButton(
                    new SubButton(
                        Translation::get('DisplayPreview'), null,
                        $this->get_url(array(self::PARAM_ACTION => self::ACTION_DISPLAY)),
                        SubButton::DISPLAY_ICON_AND_LABEL, false, array(), null, ($isDisplayAction ? true : false)
                    )
                );
            }

            if ($isDisplayAction && $previewExists && $this->getPreview()->supports_reset())
            {
                $buttonToolBar->addItem(
                    new Button(
                        Translation::get('ResetDisplayPreview'), new FontAwesomeGlyph('undo'),
                        $this->get_url(array(self::PARAM_ACTION => self::ACTION_RESET)),
                        SubButton::DISPLAY_ICON_AND_LABEL, true
                    )
                );
            }

            $dropdownButton->addSubButton(new SubButtonDivider());

            $views = array(
                ContentObjectRendition::VIEW_FULL,
                ContentObjectRendition::VIEW_PREVIEW,
                ContentObjectRendition::VIEW_THUMBNAIL,
                ContentObjectRendition::VIEW_DESCRIPTION,
                ContentObjectRendition::VIEW_FULL_THUMBNAIL,
                ContentObjectRendition::VIEW_INLINE,
                ContentObjectRendition::VIEW_FORM
            );

            foreach ($views as $view)
            {
                $isRenditionAction = $this->get_action() == self::ACTION_RENDITION;
                $isActive = ($isRenditionAction && $this->getCurrentView() == $view ? true : false);

                $dropdownButton->addSubButton(
                    new SubButton(
                        Translation::get('View' . StringUtilities::getInstance()->createString($view)->upperCamelize()),
                        null, $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_RENDITION,
                            self::PARAM_FORMAT => $this->getCurrentFormat(),
                            self::PARAM_VIEW => $view
                        )
                    ), SubButton::DISPLAY_ICON_AND_LABEL, false, array(), null, $isActive
                    )
                );
            }
        }

        return $this->buttonToolBarRenderer;
    }

    /**
     * @return string
     */
    public function getCurrentFormat()
    {
        return $this->getRequest()->query->get(self::PARAM_FORMAT, ContentObjectRendition::FORMAT_HTML);
    }

    /**
     * @return string
     */
    public function getCurrentView()
    {
        return $this->getRequest()->query->get(self::PARAM_VIEW, ContentObjectRendition::VIEW_FULL);
    }

    /**
     * @return \Chamilo\Libraries\Architecture\Application\Application
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException
     */
    public function getPreview()
    {
        $package = $this->get_content_object()->package();
        $context = $package . '\Display\Preview';

        return $this->getApplicationFactory()->getApplication(
            $context, new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this)
        );
    }

    /**
     * @return string
     */
    protected function getPreviewModeWarning()
    {
        $translator = $this->getTranslator();

        $translation =
            $translator->trans('PreviewModeWarning', array(), $this->get_content_object()->package() . '\Display');

        if ($translation == 'PreviewModeWarning')
        {
            $translation = $translator->trans('PreviewModeWarning', array(), Utilities::COMMON_LIBRARIES);
        }

        return $translation;
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function get_content_object()
    {
        return $this->content_object;
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     */
    public static function get_content_object_default_action_link($contentObject)
    {
        if (!$contentObject instanceof ContentObject && is_numeric($contentObject))
        {
            $contentObject = DataManager::retrieve_by_id(
                ContentObject::class, $contentObject
            );
        }

        if (!$contentObject instanceof ContentObject)
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
                self::PARAM_CONTENT_OBJECT_ID => $contentObject->get_id()
            )
        );

        return $redirect->getUrl();
    }

    /**
     * @return string
     */
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

    /**
     * @param string $pageTitle
     *
     * @return string
     */
    public function render_header($pageTitle = '')
    {
        $page = Page::getInstance();
        $page->setViewMode(Page::VIEW_MODE_HEADERLESS);

        $html = array();

        $html[] = $page->getHeader()->toHtml();
        $html[] = $this->renderPreviewHeader();

        return implode(PHP_EOL, $html);
    }
}
