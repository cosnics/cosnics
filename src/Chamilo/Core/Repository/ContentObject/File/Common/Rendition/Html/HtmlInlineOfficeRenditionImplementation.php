<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
abstract class HtmlInlineOfficeRenditionImplementation extends HtmlInlineRenditionImplementation
{
    // View type
    public const VIEWER_URL_EMBED = 'https://view.officeapps.live.com/op/embed.aspx?src=';

    public const VIEWER_URL_FULL = 'https://view.officeapps.live.com/op/view.aspx?src=';

    // Viewer URL

    public const VIEW_TYPE_EMBED = 'embed';

    public const VIEW_TYPE_FULL = 'full';

    /**
     * @see \Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\HtmlInlineRenditionImplementation::render()
     */
    public function render($parameters)
    {
        $html = [];

        if ($this->canBeDisplayed())
        {
            $html[] = '<div class="office-viewer-container">';

            $html[] = '<div class="office-viewer-content">';
            $html[] = '<div class="alert alert-info office-viewer-sidebar center-block">';

            $alertText = [];

            $glyph = new FontAwesomeGlyph('lock', [], null, 'fas');

            $alertText[] = $glyph->render();
            $alertText[] =
                '<span class="office-viewer-full-screen-message">' . Translation::get('OfficeViewerFullScreen') .
                '</span>';
            $alertText[] = '<a class="btn btn-default btn-office-viewer-minimize">' .
                Translation::get('OfficeViewerExitFullScreen') . '</a>';

            $html[] = implode(' ', $alertText);
            $html[] = '</div>';

            $html[] = '<iframe class="' . implode(' ', $this->getViewerFrameClasses()) . '" data-url="' .
                $this->getIFrameSource() . '">';
            $html[] = '</iframe>';

            $html[] = '</div>';

            $html[] = $this->renderActions();

            $html[] = '</div>';

            $html[] = ResourceManager::getInstance()->getResourceHtml(
                $this->getWebPathBuilder()->getJavascriptPath(File::CONTEXT) . 'OfficeViewer.js'
            );
            $html[] = ResourceManager::getInstance()->getResourceHtml(
                $this->getWebPathBuilder()->getPluginPath(StringUtilities::LIBRARIES) .
                'Jquery/jquery.fullscreen.min.js'
            );
        }
        else
        {
            $html[] = $this->getErrorMessage();
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @return bool
     */
    public function allowsFullScreen()
    {
        return $this->getViewerType() != self::VIEW_TYPE_EMBED;
    }

    /**
     * @return bool
     */
    public function canBeDisplayed()
    {
        return $this->get_content_object()->get_filesize() <= $this->getSizeLimit();
    }

    /**
     * @param string $classes
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar
     */
    public function getButtonToolbar(array $classes = [])
    {
        $buttonToolBar = parent::getButtonToolBar($classes);

        if ($this->allowsFullScreen())
        {
            $buttonToolBar->addItem(
                new Button(
                    Translation::get('ViewFullScreen'), new FontAwesomeGlyph('arrows-alt'), '#',
                    Button::DISPLAY_ICON_AND_LABEL, null, ['btn-office-viewer-full-screen']
                )
            );
        }

        return $buttonToolBar;
    }

    /**
     * @param string[] $parameters
     *
     * @return string
     */
    public function getErrorMessage()
    {
        $filesystemTools = $this->getFilesystemTools();

        $html = [];

        $html[] = '<div class="alert alert-info">';
        $html[] = '<h4>' . Translation::get('LiveViewNotSupportedTitle') . '</h4>';

        $html[] = Translation::get(
            'LiveViewNotSupported', [
                'MAX_FILESIZE' => $filesystemTools->formatFileSize($this->getSizeLimit()),
                'CURRENT_FILESIZE' => $filesystemTools->formatFileSize($this->get_content_object()->get_filesize())
            ]
        );

        $html[] = '<br /><br />';
        $html[] = $this->renderActions(['btn-info']);
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string
     */
    public function getIFrameSource()
    {
        return $this->getViewerBaseUrl() . urlencode($this->getDownloadUrl());
    }

    /**
     * @return int
     */
    abstract public function getSizeLimit();

    /**
     * @return string
     */
    public function getViewerBaseUrl()
    {
        switch ($this->getViewerType())
        {
            case self::VIEW_TYPE_FULL :
                return self::VIEWER_URL_FULL;
                break;
            case self::VIEW_TYPE_EMBED :
                return self::VIEWER_URL_EMBED;
                break;
        }
    }

    /**
     * @return string[]
     */
    public function getViewerFrameClasses()
    {
        $classes = ['office-viewer-frame'];

        if ($this->getViewerType() == self::VIEW_TYPE_EMBED)
        {
            $classes[] = 'office-viewer-frame-embed';
        }

        return $classes;
    }

    /**
     * @return string
     */
    public function getViewerType()
    {
        $viewerType = $this->getConfigurationConsulter()->getSetting([File::CONTEXT, 'office_viewer_type']);

        if (is_null($viewerType))
        {
            return self::VIEW_TYPE_FULL;
        }

        return $viewerType;
    }
}
