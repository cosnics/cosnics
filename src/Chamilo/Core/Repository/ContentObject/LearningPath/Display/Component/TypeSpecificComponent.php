<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\ApplicationSupport;

/**
 *
 * @package core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class TypeSpecificComponent extends BaseHtmlTreeComponent implements ApplicationSupport
{

    /**
     * Executes this component
     */
    public function build()
    {
        $this->validateSelectedTreeNodeData();

        $object_namespace = $this->getCurrentTreeNode()->getContentObject()->package();
        $integration_namespace = $object_namespace .
             '\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display';

        return $this->getApplicationFactory()->getApplication(
            $integration_namespace,
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this))->run();
    }

    public function get_additional_parameters()
    {
        return array(self::PARAM_CHILD_ID, self::PARAM_FULL_SCREEN);
    }
}
