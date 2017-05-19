<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Renderer\LearningPathTreeJSONMapper;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActionGenerator\NodeBaseActionGenerator;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AutomaticNumberingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathTreeBuilder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Menu\ContentObjectCategoryMenu;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Service\WorkspaceService;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Platform\Translation;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Builds the learning path tree for a given learning path by a given identifier.
 * Makes sure that the user has the correct rights for the learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GetLearningPathTreeComponent extends Manager
{
    const PARAM_LEARNING_PATH_ID = 'learning_path_id';

    /**
     * Executes this component and return's its response
     */
    public function run()
    {
        try
        {
            $learningPathId = $this->getPostDataValue(self::PARAM_LEARNING_PATH_ID);
            $learningPath = $this->getContentObjectRepository()->findById($learningPathId);

            if (!$learningPath instanceof LearningPath)
            {
                throw new ObjectNotExistException(
                    Translation::getInstance()->getTranslation('LearningPath'), $learningPathId
                );
            }

            if(!$this->getRightsService()->canCopyContentObject($this->getUser(), $learningPath))
            {
                throw new NotAllowedException();
            }

            $learningPathTree = $this->getLearningPathTreeBuilder()->buildLearningPathTree($learningPath);

            return new JsonResponse($this->convertLearningPathTreeToArray($learningPathTree));
        }
        catch (\Exception $ex)
        {
            return $this->handleException($ex);
        }
    }

    /**
     * @param LearningPathTree $learningPathTree
     *
     * @return \string[]
     */
    protected function convertLearningPathTreeToArray(LearningPathTree $learningPathTree)
    {
        $learningPathTreeJSONMapper = new LearningPathTreeJSONMapper(
            $learningPathTree, $this->getUser(),
            null,
            $this->getAutomaticNumberingService(),
            new NodeBaseActionGenerator(Translation::getInstance(), $this->get_parameters()),
            '',
            $learningPathTree->getRoot(), true, false
        );

        return $learningPathTreeJSONMapper->getNodes();
    }

    /**
     * @return array
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_LEARNING_PATH_ID);
    }

    /**
     * @return ContentObjectRepository | object
     */
    protected function getContentObjectRepository()
    {
        return $this->getService('chamilo.core.repository.workspace.repository.content_object_repository');
    }

    /**
     * @return RightsService
     */
    protected function getRightsService()
    {
        return RightsService::getInstance();
    }

    /**
     * Returns the LearningPathTreeBuilder service
     *
     * @return LearningPathTreeBuilder | object
     */
    public function getLearningPathTreeBuilder()
    {
        return $this->getService(
            'chamilo.core.repository.content_object.learning_path.service.learning_path_tree_builder'
        );
    }

    /**
     * @return AutomaticNumberingService | object
     */
    public function getAutomaticNumberingService()
    {
        return $this->getService(
            'chamilo.core.repository.content_object.learning_path.service.automatic_numbering_service'
        );
    }
}