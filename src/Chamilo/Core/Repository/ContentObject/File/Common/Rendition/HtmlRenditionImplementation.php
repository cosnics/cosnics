<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition;

use Chamilo\Core\Repository\ContentObject\File\Common\RenditionImplementation;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class HtmlRenditionImplementation extends RenditionImplementation
{
    use DependencyInjectionContainerTrait;

    public function getButtonToolBar(array $classes = [])
    {
        $object = $this->get_content_object();
        $name = $object->get_filename();

        $label = '<small>(' . $this->getFilesystemTools()->formatFileSize($object->get_filesize()) . ')</small>';

        $buttonToolBar = new ButtonToolBar();

        $buttonToolBar->addItem(
            new Button(
                Translation::get('DownloadFile', ['LABEL' => $label]), new FontAwesomeGlyph('arrow-alt-circle-down'),
                $this->getDownloadUrl(), Button::DISPLAY_ICON_AND_LABEL, null, $classes, '_blank'
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
        return $this->getUrlGenerator()->fromParameters();
    }

    /**
     * @param string[] $classes
     *
     * @return string
     */
    public function renderActions(array $classes = [])
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
            Translation::get($file->get_filename(), ['LABEL' => 'qsdf']), $file->getGlyph(IdentGlyph::SIZE_MEDIUM),
            $this->getDownloadUrl(), Button::DISPLAY_ICON_AND_LABEL, null, [], '_blank'
        );

        $buttonToolBar->addItem($button);

        $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolBarRenderer->render();
    }

    /**
     * @param array $parameters
     */
    public function renderInline($parameters = [])
    {
        /** @var File $object */
        $object = $this->get_content_object();

        if (!$object->getShowInline())
        {
            return $this->getTwig()->render(
                'Chamilo\Core\Repository\ContentObject\File:full_thumbnail.html.twig', [
                    'icon' => $object->getGlyph(IdentGlyph::SIZE_BIG)->render(),
                    'title' => $object->get_title(),
                    'download_url' => $this->getDownloadUrl()
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

        $rendition = new $class($this->get_content_object());

        return $rendition->render($parameters);
    }

}
