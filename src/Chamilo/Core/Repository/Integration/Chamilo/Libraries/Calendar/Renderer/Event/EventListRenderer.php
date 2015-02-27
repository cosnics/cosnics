<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Renderer\Event;

use Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Event\ContentObjectSupport;
use Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\AttachmentSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package core\repository\calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class EventListRenderer extends \Chamilo\Libraries\Calendar\Renderer\Event\Type\EventListRenderer
{

    /**
     *
     * @see \libraries\calendar\renderer\EventListRenderer::get_content()
     */
    public function get_content()
    {
        $html = array();

        $html[] = $this->get_event()->get_content();
        $html[] = $this->render_attachments();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param Event $event
     * @return string
     */
    public function render_attachments()
    {
        if ($this->get_event() instanceof ContentObjectSupport)
        {
            $object = $this->get_event()->get_content_object();

            if ($object instanceof AttachmentSupport)
            {
                $attachments = $object->get_attachments();

                if (count($attachments) > 0)
                {
                    Utilities :: order_content_objects_by_title($attachments);

                    $html[] = '<div class="attachments" style="margin-top: 1em;">';
                    $html[] = '<div class="attachments_title">' .
                         htmlentities(
                            Translation :: get('Attachments', null, \Chamilo\Core\Repository\Manager :: context())) .
                         '</div>';
                    $html[] = '<ul class="attachments_list">';

                    foreach ($attachments as $attachment)
                    {
                        $url = $this->get_attachment_link($this->get_event(), $attachment);

                        $render = array();

                        $render[] = '<li>';

                        if ($url)
                        {
                            $render[] = '<a onclick="javascript:openPopup(\'' . $url . '\'); return false;" href="' .
                                 $url . '">';
                        }

                        $render[] = '<img src="' . Theme :: getInstance()->getImagesPath($attachment->context()) .
                             'Logo/16.png" alt="' . htmlentities(
                                Translation :: get('TypeName', null, $attachment->context())) . '"/>';
                        $render[] = ' ';
                        $render[] = $attachment->get_title();

                        if ($url)
                        {
                            $render[] = '</a>';
                        }

                        $render[] = '</li>';

                        $html[] = implode('', $render);
                    }

                    $html[] = '</ul></div>';

                    return implode(PHP_EOL, $html);
                }
            }
        }
    }

    abstract function get_attachment_link(Event $event, ContentObject $attachment);
}
