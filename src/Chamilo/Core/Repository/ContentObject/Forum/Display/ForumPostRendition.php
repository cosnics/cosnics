<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumPost;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Forum\Display$ForumPostRendition
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class ForumPostRendition
{

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumPost
     */
    private $forumPost;

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param \Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumPost $forumPost
     */
    public function __construct(Application $application, ForumPost $forumPost)
    {
        $this->application = $application;
        $this->forumPost = $forumPost;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumPost
     */
    public function getForumPost()
    {
        return $this->forumPost;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumPost $forumPost
     */
    public function setForumPost(ForumPost $forumPost)
    {
        $this->forumPost = $forumPost;
    }

    public function render()
    {
        $renderer = new ContentObjectResourceRenderer($this, $this->getForumPost()->get_content());
        $renderedForumPost = $renderer->run();

        $renderedForumPost = preg_replace(
            '/\[quote=("|&quot;)(.*)("|&quot;)\]/',
            "<div class=\"quotetitle\">$2 " . Translation :: get('Wrote') . ":</div><div class=\"quotecontent\">",
            $renderedForumPost);
        $renderedForumPost = str_replace('[/quote]', '</div>', $renderedForumPost);

        $html[] = '<div style="overflow: auto;">';
        $html[] = $renderedForumPost;
        $html[] = '<div class="clearfix"></div>';
        $html[] = $this->get_attachments();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function get_attachments()
    {
        $forumPost = $this->getForumPost();
        $html = array();

        $attachments = $forumPost->get_attached_content_objects();

        if (count($attachments))
        {
            $html[] = '<div class="attachments" style="margin-top: 1em;">';
            $html[] = '<div class="attachments_title">' . htmlentities(Translation :: get('Attachments')) . '</div>';
            Utilities :: order_content_objects_by_title($attachments);
            $html[] = '<ul class="attachments_list">';

            foreach ($attachments as $attachment)
            {
                $params = array();
                $params[Manager :: PARAM_ACTION] = Manager :: ACTION_VIEW_ATTACHMENT;
                $params[Manager :: PARAM_FORUM_TOPIC_ID] = $forumPost->get_forum_topic_id();
                $params[Manager :: PARAM_SELECTED_FORUM_POST] = $forumPost->get_id();
                $params[Manager :: PARAM_ATTACHMENT_ID] = $attachment->get_id();

                $url = $this->getApplication()->get_url($params);
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

        return implode(PHP_EOL, $html);
    }
}
