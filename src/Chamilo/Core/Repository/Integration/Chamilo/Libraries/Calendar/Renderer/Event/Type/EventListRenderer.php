<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Renderer\Event\Type;

use Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Event\ContentObjectSupport;
use Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\AttachmentSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Renderer\Event\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class EventListRenderer extends \Chamilo\Libraries\Calendar\Renderer\Event\Type\EventListRenderer
{

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
                    Utilities::order_content_objects_by_title($attachments);
                    
                    $html[] = '<div class="attachments" style="margin-top: 1em;">';
                    $html[] = '<div class="attachments_title">' .
                         htmlentities(
                            Translation::get('Attachments', null, \Chamilo\Core\Repository\Manager::context())) . '</div>';
                    $html[] = '<ul class="attachments_list">';
                    
                    foreach ($attachments as $attachment)
                    {
                        $url = $this->getAttachmentLink($this->getEvent(), $attachment);
                        
                        $render = array();
                        
                        $render[] = '<li>';
                        
                        if ($url)
                        {
                            $render[] = '<a onclick="javascript:openPopup(\'' . $url . '\'); return false;" href="' .
                                 $url . '">';
                        }
                        
                        $render[] = '<img src="' . Theme::getInstance()->getImagePath($attachment->context(), 'Logo/16') .
                             '" alt="' . htmlentities(Translation::get('TypeName', null, $attachment->context())) . '"/>';
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

    abstract function getAttachmentLink(Event $event, ContentObject $attachment);
}
