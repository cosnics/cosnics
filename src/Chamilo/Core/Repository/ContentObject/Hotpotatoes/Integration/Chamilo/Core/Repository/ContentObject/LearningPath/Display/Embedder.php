<?php
namespace Chamilo\Core\Repository\ContentObject\Hotpotatoes\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder\Type\ContentObjectEmbedder;
use Chamilo\Libraries\File\Path;

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
        $attempt_data = $this->get_node()->get_data();
        
        // TODO: Make this implementation context-independent
        $link = $this->get_content_object()->add_javascript(
            Path :: getInstance()->getBasePath(true) . 'application/weblcms/php/ajax/lp_hotpotatoes_save_score.php', 
            null, 
            $attempt_data->get_id());
        
        $html = array();
        
        $html[] = '<iframe frameborder="0" class="link_iframe" src="' . $link . '" width="100%" height="700px">';
        $html[] = '<p>Your browser does not support iframes.</p></iframe>';
        
        return implode(PHP_EOL, $html);
    }
}
