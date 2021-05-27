<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display\Component;

use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Page;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Forum\Display\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class TopicPreviewerComponent extends TopicViewerComponent implements DelegateComponent
{

    public function run()
    {
        Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);
        
        $html = [];
        
        $html[] = $this->render_header();
        $html[] = $this->renderPosts();
        $html[] = $this->renderPager();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumPost[]
     */
    public function getVisibleForumTopicPosts()
    {
        return array_slice(
            array_reverse($this->getForumTopicPosts()), 
            $this->getPager()->getCurrentRangeOffset(), 
            $this->getItemsPerPage());
    }
}
