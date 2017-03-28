<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Assessment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

class BuilderComponent extends Manager
{

    /**
     *
     * @var ContentObject
     */
    protected $rootContentObject;

    function run()
    {
        $learningPathTreeNode = $this->getCurrentLearningPathTreeNode();
        
        if (! $this->get_application()->canEditLearningPathTreeNode($learningPathTreeNode))
        {
            throw new NotAllowedException();
        }
        
        $this->rootContentObject = $learningPathTreeNode->getContentObject();
        
        $context = ClassnameUtilities::getInstance()->getNamespaceParent($this->rootContentObject->get_type(), 3) .
             '\Builder';
        
        $application_factory = new ApplicationFactory(
            $context, 
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        
        return $application_factory->run();
    }

    /**
     *
     * @return ContentObject
     */
    public function get_root_content_object()
    {
        return $this->rootContentObject;
    }
}
