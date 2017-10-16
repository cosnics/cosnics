<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\Assessment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
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
        $learningPathTreeNode = $this->getCurrentTreeNode();

        if (! $this->get_application()->canEditTreeNode($learningPathTreeNode))
        {
            throw new NotAllowedException();
        }

        $this->rootContentObject = $learningPathTreeNode->getContentObject();

        $context = ClassnameUtilities::getInstance()->getNamespaceParent($this->rootContentObject->get_type(), 3) .
             '\Builder';

        return $this->getApplicationFactory()->getApplication(
            $context,
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this))->run();
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
