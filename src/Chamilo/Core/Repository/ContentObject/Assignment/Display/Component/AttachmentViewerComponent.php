<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AttachmentViewerComponent extends Manager
{

    public function run()
    {
        $attachment_id = Request::get(\Chamilo\Core\Repository\Display\Manager::PARAM_ATTACHMENT_ID);
        if (is_null($attachment_id))
        {
            throw new ParameterNotDefinedException(\Chamilo\Core\Repository\Display\Manager::PARAM_ATTACHMENT_ID);
        }

        /** @var ContentObject $attachment */
        $attachment = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class_name(),
            $attachment_id
        );

        $entry = $this->getEntry();

        if (!$this->get_root_content_object()->is_attached_to_or_included_in($attachment_id))
        {
            if(!$entry instanceof Entry || !$this->getAssignmentServiceBridge()->isContentObjectAttachedToEntry($entry, $attachment))
            {
                throw new NotAllowedException();
            }
        }

        /*
         * Render the attachment
         */
        $trail = BreadcrumbTrail::getInstance();
        $trail->add(
            new Breadcrumb(
                $this->get_url(array(\Chamilo\Core\Repository\Display\Manager::PARAM_ATTACHMENT_ID => $attachment_id)),
                Translation::get('ViewAttachment')
            )
        );

        Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);

        $html = array();

        $html[] = $this->render_header();
        $html[] = ContentObjectRenditionImplementation::launch(
            $attachment,
            ContentObjectRendition::FORMAT_HTML,
            ContentObjectRendition::VIEW_FULL,
            $this
        );
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::render_header()
     */
    public function render_header($pageTitle = '')
    {
        return Page::getInstance()->getHeader()->toHtml();
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::render_footer()
     */
    public function render_footer()
    {
        return Page::getInstance()->getFooter()->toHtml();
    }
}
