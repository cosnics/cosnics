<?php
namespace Chamilo\Core\Repository\Common\Rendition\Html\Type;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\Common\Rendition\Html\HtmlContentObjectRendition;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\AttachmentSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class HtmlDescriptionContentObjectRendition extends HtmlContentObjectRendition
{

    public function render()
    {
        $html[] = '<div style="overflow: auto;">';
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

    public function get_attachments()
    {
        $object = $this->get_content_object();
        $html = array();

        if ($object instanceof AttachmentSupport)
        {
            $attachments = $object->get_attachments();
            if (count($attachments))
            {
                $html[] = '<div class="attachments" style="margin-top: 1em;">';
                $html[] = '<div class="attachments_title">' . htmlentities(Translation :: get('Attachments')) . '</div>';
                Utilities :: order_content_objects_by_title($attachments);
                $html[] = '<ul class="attachments_list">';
                foreach ($attachments as $attachment)
                {
                    $url = $this->get_context()->get_content_object_display_attachment_url($attachment);
                    $url = 'javascript:openPopup(\'' . $url . '\'); return false;';
                    $html[] = '<li><a href="#" onClick="' . $url . '"><img src="' . Theme :: getInstance()->getImagePath(
                        $attachment->package(),
                        'Logo/' . Theme :: ICON_MINI) . '" alt="' .
                         htmlentities(
                            Translation :: get(
                                'TypeName',
                                null,
                                ClassnameUtilities :: getInstance()->getNamespaceFromClassname($attachment->get_type()))) .
                         '"/> ' . $attachment->get_title() . '</a></li>';
                }
                $html[] = '</ul>';
                $html[] = '</div>';
            }
        }

        return implode(PHP_EOL, $html);
    }
}
