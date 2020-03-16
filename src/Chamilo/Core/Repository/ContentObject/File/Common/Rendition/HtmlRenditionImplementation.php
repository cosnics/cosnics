<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition;

use Chamilo\Core\Repository\ContentObject\File\Common\RenditionImplementation;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class HtmlRenditionImplementation extends RenditionImplementation
{
    use DependencyInjectionContainerTrait;

    public function getButtonToolBar($classes = '')
    {
        $object = $this->get_content_object();
        $name = $object->get_filename();

        $label = '<small>(' . Filesystem::format_file_size($object->get_filesize()) . ')</small>';

        $buttonToolBar = new ButtonToolBar();

        $buttonToolBar->addItem(
            new Button(
                Translation::get('DownloadFile', array('LABEL' => $label)), new FontAwesomeGlyph('arrow-circle-o-down'),
                $this->getDownloadUrl(), Button::DISPLAY_ICON_AND_LABEL, false, $classes, '_blank'
            )
        );

        return $buttonToolBar;
    }

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function getDownloadUrl()
    {
        /** @var File $object */
        $object = $this->get_content_object();

        $fullPath = $object->get_full_path();
        $timestamp = filemtime($fullPath);

        return Manager::get_document_downloader_url(
                $object->get_id(), $object->calculate_security_code()
            ) . '&time=' . $timestamp;
    }

    /**
     * @param ContentObject $contentObject
     *
     * @return string
     */
    protected function getPopupUrl(ContentObject $contentObject)
    {
        $redirect = new Redirect();

        return $redirect->getUrl();
    }

    /**
     *
     * @param string $classes
     *
     * @return string
     */
    public function renderActions($classes = '')
    {
        $buttonToolBarRenderer = new ButtonToolBarRenderer($this->getButtonToolBar($classes));

        return $buttonToolBarRenderer->render();
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File $file
     *
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function renderCompactView(File $file)
    {
        $buttonToolBar = new ButtonToolBar();

        $button = new Button(
        //Translation::get('DownloadFile', array('LABEL' => 'qsdf')),
            Translation::get($file->get_filename(), array('LABEL' => 'qsdf')), $file->getGlyph(Theme::ICON_MEDIUM),
            $this->getDownloadUrl(), Button::DISPLAY_ICON_AND_LABEL, false, '', '_blank'
        );

        $buttonToolBar->addItem($button);

        $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolBarRenderer->render();
    }

    /**
     *
     * @param array $parameters
     */
    public function renderInline($parameters = array())
    {
        /** @var File $object */
        $object = $this->get_content_object();

        if (!$object->getShowInline())
        {
            $this->initializeContainer();

            return $this->getTwig()->render(
                'Chamilo\Core\Repository\ContentObject\File:full_thumbnail.html.twig', [
                    "icon"    => $object->getGlyph(Theme::ICON_BIG)->render(),
                    "title"        => $object->get_title(),
                    "download_url" => $this->getDownloadUrl()
                ]
            );
        }

        $class = __NAMESPACE__ . '\Html\Extension\HtmlInline' .
            (string) StringUtilities::getInstance()->createString($object->get_extension())->upperCamelize() .
            'RenditionImplementation';

        if (!class_exists($class))
        {
            $document_type = $object->determine_type();
            $class = __NAMESPACE__ . '\Html\Type\HtmlInline' .
                (string) StringUtilities::getInstance()->createString($document_type)->upperCamelize() .
                'RenditionImplementation';
        }

        $rendition = new $class($this->get_context(), $this->get_content_object());

        return $rendition->render($parameters);
    }

}
