<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\Forum\Display\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Format\Structure\Page;

/**
 * Attachment Viewer used for the Forum Posts.
 *
 * @author Maarten Volckaert - Hogeschool Gent
 */
class AttachmentViewerComponent extends Manager
{

    public function run()
    {
        /*
         * Retrieve data and check if it is a valid attachment
         */
        $post_id = Request :: get(self :: PARAM_SELECTED_FORUM_POST);
        $attachment_id = Request :: get(self :: PARAM_ATTACHMENT_ID);
        $topic_id = Request :: get(self :: PARAM_FORUM_TOPIC_ID);

        if (is_null($attachment_id))
        {
            throw new ParameterNotDefinedException(self :: PARAM_ATTACHMENT_ID);
        }

        /*
         * Test if a attachment is attached to a post.
         */
        $found_post = DataManager :: retrieve_attached_object($post_id, $attachment_id);

        if ($found_post == null)
        {
            throw new NotAllowedException();
        }

        /*
         * Test if a post is part of a topic
         */
        $post_in_topic = DataManager :: retrieve_forum_post_of_topic($topic_id, $post_id);

        if ($post_in_topic == null)
        {
            throw new NotAllowedException();
        }

        /*
         * Render the attachment
         */
        $attachment = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object($attachment_id);
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(
            new Breadcrumb(
                $this->get_url(array(self :: PARAM_ATTACHMENT_ID => $attachment_id)),
                Translation :: get('ViewAttachment')));

        Page :: getInstance()->setViewMode(Page :: VIEW_MODE_HEADERLESS);

        $html = array();

        $html[] = $this->render_header();
        $html[] = '<a href="javascript:history.go(-1)">' .
             Translation :: get('Back', null, Utilities :: COMMON_LIBRARIES) . '</a><br /><br />';
        $html[] = ContentObjectRenditionImplementation :: launch(
            $attachment,
            ContentObjectRendition :: FORMAT_HTML,
            ContentObjectRendition :: VIEW_FULL,
            $this);
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);

        // END EDITING
    }
}
