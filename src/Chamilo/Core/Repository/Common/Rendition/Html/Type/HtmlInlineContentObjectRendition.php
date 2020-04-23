<?php
namespace Chamilo\Core\Repository\Common\Rendition\Html\Type;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\Common\Rendition\Html\HtmlContentObjectRendition;
use Chamilo\Libraries\Architecture\Interfaces\AttachmentSupport;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;

class HtmlInlineContentObjectRendition extends HtmlContentObjectRendition
{

    public function render()
    {
        $object = $this->get_content_object();
        $html = array();
        $html[] = '<div class="panel panel-default">';

        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">' . $object->get_icon_image() . ' ' . $object->get_title() . '</h3>';
        $html[] = '</div>';

        $html[] = $this->get_description();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function get_attachments()
    {
        $object = $this->get_content_object();

        $html = array();

        if ($object instanceof AttachmentSupport)
        {
            $attachments = $object->get_attachments();

            if (count($attachments))
            {
                $html[] = '<div class="panel panel-default panel-attachments">';
                $glyph = new FontAwesomeGlyph('paperclip', array(), null, 'fas');
                $html[] = '<div class="panel-heading">' . $glyph->render() . ' ' .
                    htmlentities(Translation::get('Attachments')) . '</div>';

                usort(
                    $attachments, function ($contentObjectOne, $contentObjectTwo) {
                    return strcasecmp($contentObjectOne->get_title(), $contentObjectTwo->get_title());
                }
                );

                $html[] = '<ul class="list-group">';

                /**
                 * @var \Chamilo\Core\Repository\Storage\DataClass\ContentObject[] $attachments
                 * @var \Chamilo\Core\Repository\Storage\DataClass\ContentObject $attachment
                 */
                foreach ($attachments as $attachment)
                {
                    $url = $this->get_context()->get_content_object_display_attachment_url($attachment);
                    $url = 'javascript:openPopup(\'' . $url . '\'); return false;';

                    $glyph = $attachment->getGlyph();

                    $html[] =
                        '<li class="list-group-item"><a href="#" onClick="' . $url . '">' . $glyph->render() . ' ' .
                        $attachment->get_title() . '</a></li>';
                }
                $html[] = '</ul>';
                $html[] = '</div>';
            }
        }

        return implode(PHP_EOL, $html);
    }

    public function get_description()
    {
        $html[] = '<div class="panel-body" style="overflow: auto;">';
        $renderer = new ContentObjectResourceRenderer($this, $this->get_content_object()->get_description());
        $html[] = $renderer->run();
        $html[] = '<div class="clearfix"></div>';

        if (method_exists($this->get_rendition_implementation(), 'get_description'))
        {
            $html[] = $this->get_rendition_implementation()->get_description();
        }

        $html[] = $this->get_attachments();

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
