<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder;

use Chamilo\Core\Repository\ContentObject\LearningPath\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\TreeNodeAttempt;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder\Type\ContentObjectEmbedder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathTrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Embedder
{
    const PARAM_EMBEDDED_CONTENT_OBJECT_ID = 'embedded_content_object_id';

    /**
     *
     * @var Application
     */
    private $application;

    /**
     * @var LearningPathTrackingService
     */
    protected $learningPathTrackingService;

    /**
     * @var LearningPath
     */
    protected $learningPath;

    /**
     * @var TreeNode
     */
    protected $treeNode;

    /**
     * Embedder constructor.
     *
     * @param Application $application
     * @param LearningPathTrackingService $learningPathTrackingService
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     */
    public function __construct(
        \Chamilo\Libraries\Architecture\Application\Application $application,
        LearningPathTrackingService $learningPathTrackingService,
        LearningPath $learningPath,
        TreeNode $treeNode
    )
    {
        $this->application = $application;
        $this->learningPathTrackingService = $learningPathTrackingService;
        $this->learningPath = $learningPath;
        $this->treeNode = $treeNode;
    }

    /**
     * @return Application
     */
    public function get_application()
    {
        return $this->application;
    }

    /**
     *
     * @return string
     */
    public function track()
    {
        $this->learningPathTrackingService->changeActiveAttemptStatus(
            $this->learningPath, $this->treeNode, $this->get_application()->getUser()
        );

        $activeAttemptId = $this->learningPathTrackingService->getActiveAttemptId(
            $this->learningPath, $this->treeNode, $this->get_application()->getUser()
        );

        // We need the second parent as the first one is just the display itself, since the embedder is a child of the
        // display execution wise and the required context is that of the display itself
        $namespace = $this->get_application()->get_application()->package() .
            '\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax';

        $html[] = '<script type="text/javascript">';
        $html[] = '    var trackerId = "' . $activeAttemptId . '";';
        $html[] = '    var trackerContext = ' . json_encode($namespace) . ';';
        $html[] = '</script>';

        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\LearningPath\Display', true) .
            'LearningPathItem.js'
        );

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function run()
    {
        $html = array();

        $html[] = $this->track();
        $html[] = $this->render();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    abstract public function render();

    /**
     *
     * @param Application $application
     * @param LearningPathTrackingService $learningPathTrackingService
     * @param LearningPath $learningPath
     * @param TreeNode $treeNode
     *
     * @return Embedder
     */
    static public function factory(
        Application $application,
        LearningPathTrackingService $learningPathTrackingService,
        LearningPath $learningPath,
        TreeNode $treeNode
    )
    {
        $namespace = $treeNode->getContentObject()->package() .
            '\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display';
        $class_name = $namespace . '\Embedder';

        if (!class_exists($class_name))
        {
            return new ContentObjectEmbedder(
                $application, $learningPathTrackingService, $learningPath, $treeNode
            );
        }

        return new $class_name($application, $learningPathTrackingService, $learningPath, $treeNode);
    }

    /**
     *
     * @return int
     */
    static public function get_embedded_content_object_id()
    {
        return Request::get(self::PARAM_EMBEDDED_CONTENT_OBJECT_ID);
    }
}