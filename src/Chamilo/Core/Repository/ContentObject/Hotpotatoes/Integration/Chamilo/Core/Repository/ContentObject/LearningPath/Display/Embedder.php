<?php
namespace Chamilo\Core\Repository\ContentObject\Hotpotatoes\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Core\Repository\ContentObject\Hotpotatoes\Storage\DataClass\Hotpotatoes;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder\Type\ContentObjectEmbedder;
use Chamilo\Libraries\File\Redirect;

/**
 *
 * @package core\repository\content_object\hotpotatoes\integration\core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Embedder extends ContentObjectEmbedder
{

    /**
     *
     * @see \core\repository\content_object\learning_path\display\Embedder::run()
     */
    public function run()
    {
        $attempt_data = $this->get_node()->get_current_attempt();

        // TODO: Make this implementation context-independent

        $redirect = new Redirect(
            array(
                \Chamilo\Application\Weblcms\Manager::PARAM_CONTEXT =>
                    \Chamilo\Application\Weblcms\Ajax\Manager::context(),
                \Chamilo\Application\Weblcms\Manager::PARAM_ACTION =>
                    \Chamilo\Application\Weblcms\Ajax\Manager::ACTION_SAVE_LEARNING_PATH_HOTPOTATOES_SCORE
            )
        );

        /** @var Hotpotatoes $hotpotoatoes */
        $hotpotoatoes = $this->learningPathTreeNode->getContentObject();

        $link = $hotpotoatoes->add_javascript(
            $redirect->getUrl(),
            null,
            $attempt_data->get_id()
        );

        $html = array();

        $html[] = '<iframe frameborder="0" class="link_iframe" src="' . $link . '" width="100%" height="700px">';
        $html[] = '<p>Your browser does not support iframes.</p></iframe>';

        return implode(PHP_EOL, $html);
    }
}
