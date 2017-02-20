<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder;

use Chamilo\Core\Repository\ContentObject\LearningPath\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Attempt\AbstractItemAttempt;
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
     * @var \libraries\architecture\application\Application
     */
    private $application;

    /**
     *
     * @var \core\repository\content_object\learning_path\ComplexContentObjectPathNode
     */
    private $node;

    /**
     *
     * @param \libraries\architecture\application\Application $application
     * @param \core\repository\content_object\learning_path\ComplexContentObjectPathNode $node
     */
    public function __construct(\Chamilo\Libraries\Architecture\Application\Application $application, 
        ComplexContentObjectPathNode $node)
    {
        $this->application = $application;
        $this->node = $node;
    }

    /**
     *
     * @return \libraries\architecture\application\Application
     */
    public function get_application()
    {
        return $this->application;
    }

    /**
     *
     * @param \libraries\architecture\application\Application $application
     */
    public function set_application(\Chamilo\Libraries\Architecture\Application\Application $application)
    {
        $this->application = $application;
    }

    /**
     *
     * @return \core\repository\content_object\learning_path\ComplexContentObjectPathNode
     */
    public function get_node()
    {
        return $this->node;
    }

    /**
     *
     * @param \core\repository\content_object\learning_path\ComplexContentObjectPathNode $node
     */
    public function set_node($node)
    {
        $this->node = $node;
    }

    /**
     *
     * @return string
     */
    public function track()
    {
        $attempt_data = $this->get_node()->get_current_attempt();
        
        $attempt_data->set_status(AbstractItemAttempt::STATUS_COMPLETED);
        $attempt_data->update();

        $this->get_node()->recalculateIsCompleted();
        $this->get_application()->recalculateLearningPathProgress();
        
        // We need the second parent as the first one is just the display itself, since the embedder is a child of the
        // display execution wise and the required context is that of the display itself
        $namespace = $this->get_application()->get_application()->package() .
             '\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax';
        
        $html[] = '<script type="text/javascript">';
        $html[] = '    var trackerId = "' . $attempt_data->get_id() . '";';
        $html[] = '    var trackerContext = ' . json_encode($namespace) . ';';
        $html[] = '</script>';
        
        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\LearningPath\Display', true) .
                 'LearningPathItem.js');
        
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
     * @param \libraries\architecture\application\Application $application
     * @param ComplexContentObjectPathNode $node
     * @return Embedder
     */
    static public function factory(\Chamilo\Libraries\Architecture\Application\Application $application, 
        ComplexContentObjectPathNode $node)
    {
        $namespace = $node->get_content_object()->package() .
             '\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display';
        $class_name = $namespace . '\Embedder';
        
        return new $class_name($application, $node);
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