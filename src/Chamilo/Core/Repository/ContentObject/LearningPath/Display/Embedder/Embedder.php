<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder\Type\ContentObjectEmbedder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\WebPathBuilder;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Request;

/**
 * @package core\repository\content_object\learning_path\display
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Embedder
{
    public const PARAM_EMBEDDED_CONTENT_OBJECT_ID = 'embedded_content_object_id';

    /**
     * @var LearningPath
     */
    protected $learningPath;

    /**
     * @var TrackingService
     */
    protected $trackingService;

    /**
     * @var TreeNode
     */
    protected $treeNode;

    /**
     * @var Application
     */
    private $application;

    /**
     * Embedder constructor.
     *
     * @param Application $application
     * @param TrackingService $trackingService
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     */
    public function __construct(
        Application $application, TrackingService $trackingService, LearningPath $learningPath, TreeNode $treeNode
    )
    {
        $this->application = $application;
        $this->trackingService = $trackingService;
        $this->learningPath = $learningPath;
        $this->treeNode = $treeNode;
    }

    /**
     * @return string
     */
    public function run()
    {
        $html = [];

        $html[] = $this->get_application()->render_header();
        $html[] = $this->track();
        $html[] = $this->render();
        $html[] = $this->get_application()->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string
     */
    abstract public function render();

    /**
     * @param Application $application
     * @param TrackingService $trackingService
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     *
     * @return Embedder
     */
    public static function factory(
        Application $application, TrackingService $trackingService, LearningPath $learningPath, TreeNode $treeNode
    )
    {
        $namespace = $treeNode->getContentObject()::CONTEXT .
            '\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display';
        $class_name = $namespace . '\Embedder';

        if (!class_exists($class_name))
        {
            return new ContentObjectEmbedder(
                $application, $trackingService, $learningPath, $treeNode
            );
        }

        return new $class_name($application, $trackingService, $learningPath, $treeNode);
    }

    /**
     * @return Application
     */
    public function get_application()
    {
        return $this->application;
    }

    /**
     * @return int
     */
    public static function get_embedded_content_object_id()
    {
        return Request::get(self::PARAM_EMBEDDED_CONTENT_OBJECT_ID);
    }

    /**
     * @return string
     */
    public function track()
    {
        $this->trackingService->setActiveAttemptCompleted(
            $this->learningPath, $this->treeNode, $this->get_application()->getUser()
        );

        $activeAttemptId = $this->trackingService->getActiveAttemptId(
            $this->learningPath, $this->treeNode, $this->get_application()->getUser()
        );

        // We need the second parent as the first one is just the display itself, since the embedder is a child of the
        // display execution wise and the required context is that of the display itself
        $namespace = $this->get_application()->get_application()::CONTEXT .
            '\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax';

        $html[] = '<script>';
        $html[] = '    var trackerId = "' . $activeAttemptId . '";';
        $html[] = '    var trackerContext = ' . json_encode($namespace) . ';';
        $html[] = '</script>';

        $container = DependencyInjectionContainerBuilder::getInstance()->createContainer();

        /**
         * @var \Chamilo\Libraries\Format\Utilities\ResourceManager $resourceManager
         */
        $resourceManager = $container->get(ResourceManager::class);

        /**
         * @var \Chamilo\Libraries\File\WebPathBuilder $webPathBuilder
         */
        $webPathBuilder = $container->get(WebPathBuilder::class);

        $html[] = $resourceManager->getResourceHtml(
            $webPathBuilder->getJavascriptPath(Manager::CONTEXT) . 'LearningPathItem.js'
        );

        return implode(PHP_EOL, $html);
    }
}