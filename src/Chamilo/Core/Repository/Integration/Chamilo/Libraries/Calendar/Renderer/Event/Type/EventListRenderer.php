<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Renderer\Event\Type;

use Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Event\ContentObjectSupport;
use Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\AttachmentSupport;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Renderer\Event\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class EventListRenderer extends \Chamilo\Libraries\Calendar\Renderer\Event\Type\EventListRenderer
{

    abstract function getAttachmentLink(Event $event, ContentObject $attachment);

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Event\Type\EventListRenderer::getContent()
     */
    public function getContent()
    {
        $html = array();

        $html[] = $this->getEvent()->getContent();
        $html[] = $this->renderAttachments();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param Event $event
     *
     * @return string
     */
    public function renderAttachments()
    {
        if ($this->getEvent() instanceof ContentObjectSupport)
        {
            $object = $this->getEvent()->getContentObject();

            if ($object instanceof AttachmentSupport)
            {
                $attachments = $object->get_attachments();

                if (count($attachments) > 0)
                {
                    usort(
                        $attachments, function ($contentObjectOne, $contentObjectTwo) {
                        return strcasecmp($contentObjectOne->get_title(), $contentObjectTwo->get_title());
                    }
                    );

                    $glyph = new FontAwesomeGlyph('paperclip', array(), null, 'fas');

                    $html[] = '<div class="panel panel-default panel-attachments">';
                    $html[] = '<div class="panel-heading">' . $glyph->render() . ' ' . htmlentities(
                            Translation::get('Attachments', null, Manager::context())
                        ) . '</div>';
                    $html[] = '<ul class="list-group">';

                    foreach ($attachments as $attachment)
                    {
                        $url = $this->getAttachmentLink($this->getEvent(), $attachment);

                        $render = array();

                        $render[] = '<li class="list-group-item">';

                        if ($url)
                        {
                            $render[] =
                                '<a onclick="javascript:openPopup(\'' . $url . '\'); return false;" href="' . $url .
                                '">';
                        }

                        $glyph = new NamespaceIdentGlyph(
                            $attachment->context(), true, false, false, IdentGlyph::SIZE_MINI, array('fa-fw')
                        );

                        $render[] = $glyph->render();
                        $render[] = ' ';
                        $render[] = $attachment->get_title();

                        if ($url)
                        {
                            $render[] = '</a>';
                        }

                        $render[] = '</li>';

                        $html[] = implode('', $render);
                    }

                    $html[] = '</ul>';
                    $html[] = '</div>';

                    return implode(PHP_EOL, $html);
                }
            }
        }
    }
}
