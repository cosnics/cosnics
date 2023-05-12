<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display\Preview\Component;

use Chamilo\Core\Repository\ContentObject\Forum\Display\ForumDisplaySupport;
use Chamilo\Core\Repository\ContentObject\Forum\Display\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;

class ViewerComponent extends \Chamilo\Core\Repository\ContentObject\Forum\Display\Preview\Manager implements
    ForumDisplaySupport
{

    public function run()
    {
        return $this->getApplicationFactory()->getApplication(
            Manager::CONTEXT,
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this))->run();
    }

    /**
     * Since this is a preview, no actual view event is triggered.
     *
     * @param $complex_topic_id
     */
    public function forum_topic_viewed($complex_topic_id)
    {
    }

    /**
     * Since this is a preview, no views are logged and no count can be retrieved.
     *
     * @param $complex_topic_id
     * @return string
     */
    public function forum_count_topic_views($complex_topic_id)
    {
        return '-';
    }

    /**
     * checks wether the user is forum manager here in repository is always true
     *
     * @param type $user
     *
     * @return true
     */
    public function is_forum_manager($user)
    {
        return true;
    }
}
