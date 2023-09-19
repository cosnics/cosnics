<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;

/**
 * @package core\repository\content_object\learning_path\display
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class TypeSpecificComponent extends BaseHtmlTreeComponent implements BreadcrumbLessComponentInterface
{

    /**
     * Executes this component
     */
    public function build()
    {
        $this->validateSelectedTreeNodeData();

        $object_namespace = $this->getCurrentTreeNode()->getContentObject()::CONTEXT;
        $integration_namespace =
            $object_namespace . '\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display';

        return $this->getApplicationFactory()->getApplication(
            $integration_namespace, new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
        )->run();
    }
}
