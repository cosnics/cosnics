<?php
namespace Chamilo\Core\Repository\ContentObject\HotspotQuestion\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\HotspotQuestion\Common\Rendition\HtmlRenditionImplementation;
use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\File\ImageManipulation\ImageManipulation;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Nette\Utils\Image;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{

    private $colours = [
        '#ff0000',
        '#f2ef00',
        '#00ff00',
        '#00ffff',
        '#0000ff',
        '#ff00ff',
        '#0080ff',
        '#ff0080',
        '#00ff80',
        '#ff8000',
        '#8000ff'
    ];

    public function render()
    {
        return ContentObjectRendition::launch($this);
    }

    public function get_description()
    {
        $html = [];

        $content_object = $this->get_content_object();
        $options = $content_object->get_answers();
        $image = $content_object->get_image_object();

        if (!is_null($image))
        {
            $dimensions = getimagesize($image->get_full_path());

            $scaledDimensions = ImageManipulation::rescale($dimensions[0], $dimensions[1], 600, 450);

            $html[] = '<div id="hotspot_container"><div id="hotspot_image" style="width: ' .
                $scaledDimensions[ImageManipulation::DIMENSION_WIDTH] . 'px; height: ' .
                $scaledDimensions[ImageManipulation::DIMENSION_HEIGHT] . 'px; background-size: ' .
                $scaledDimensions[ImageManipulation::DIMENSION_WIDTH] . 'px ' .
                $scaledDimensions[ImageManipulation::DIMENSION_HEIGHT] . 'px;background-image: url(' .
                Manager::get_document_downloader_url(
                    $image->get_id(), $image->calculate_security_code()
                ) . ')"></div></div>';
        }
        else
        {
            $html[] = '<div id="hotspot_container"><div id="hotspot_image"></div></div>';
        }
        $html[] = '';

        $html[] = '<table class="table table-striped table-bordered table-hover table-data">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th></th>';
        $html[] = '<th>' . Translation::get('HotspotTableTitle') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        foreach ($options as $index => $option)
        {
            $html[] = '<tr class="' . ($index % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td><div class="colour_box" style="background-color: ' . $this->colours[$index] .
                ';"></div><div id="coordinates_' . $index . '" class="coordinates" style="display: none;">' .
                $option->get_hotspot_coordinates() . '</div></td>';

            $renderer = new ContentObjectResourceRenderer($option->get_answer());
            $html[] = '<td>' . $renderer->run() . '</td>';

            $html[] = '</tr>';
        }
        $html[] = '</tbody>';
        $html[] = '</table>';

        $html[] = ResourceManager::getInstance()->getResourceHtml(
            $this->getWebPathBuilder()->getPluginPath('Chamilo\Core\Repository\ContentObject\HotspotQuestion') .
            'jquery.draw.js'
        );
        $html[] = ResourceManager::getInstance()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\HotspotQuestion') .
            'Rendition.js'
        );

        return implode(PHP_EOL, $html);
    }
}
