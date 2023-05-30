<?php
namespace Chamilo\Core\Repository\ContentObject\Hotpotatoes\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Application\Weblcms\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\Hotpotatoes\Storage\DataClass\Hotpotatoes;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder\Type\ContentObjectEmbedder;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;

/**
 * @package core\repository\content_object\hotpotatoes\integration\core\repository\content_object\learning_path\display
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Embedder extends ContentObjectEmbedder
{
    /**
     * @see \core\repository\content_object\learning_path\display\Embedder::run()
     */
    public function run()
    {
        $activeAttemptId = $this->trackingService->getActiveAttemptId(
            $this->learningPath, $this->treeNode, $this->get_application()->getUser()
        );

        $saveScoreUrl = $this->getUrlGenerator()->fromParameters(
            [
                \Chamilo\Application\Weblcms\Manager::PARAM_CONTEXT => Manager::CONTEXT,
                \Chamilo\Application\Weblcms\Manager::PARAM_ACTION => Manager::ACTION_SAVE_LEARNING_PATH_HOTPOTATOES_SCORE
            ]
        );

        /** @var Hotpotatoes $hotpotatoes */
        $hotpotatoes = $this->treeNode->getContentObject();

        $link = $hotpotatoes->add_javascript(
            $saveScoreUrl, null, $activeAttemptId
        );

        $html = [];

        $html[] = $this->get_application()->render_header();
        $html[] = '<iframe frameborder="0" class="link_iframe" src="' . $link . '" width="100%" height="700px">';
        $html[] = '<p>Your browser does not support iframes.</p></iframe>';
        $html[] = $this->get_application()->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(UrlGenerator::class);
    }
}
