<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\ApplicationSupport;

/**
 *
 * @package core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class TypeSpecificComponent extends TabComponent implements ApplicationSupport
{

    /**
     * Executes this component
     */
    public function build()
    {
        $object_namespace = $this->get_current_node()->get_content_object()->package();
        $integration_namespace = $object_namespace . '\Integration\\' . __NAMESPACE__;

        $factory = new ApplicationFactory($integration_namespace, new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        return $factory->run();
    }
   
}
