<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActionGenerator\NodeBaseActionGenerator;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AutomaticNumberingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeJSONMapper;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Translation\Translation;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Builds the learning path tree for a given learning path by a given identifier.
 * Makes sure that the user has the correct rights for the learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GetTreeComponent extends Manager
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

            if (!$this->getWorkspaceRightsService()->canCopyContentObject($this->getUser(), $learningPath))
            {
                throw new NotAllowedException();
            }

            $tree = $this->getLearningPathService()->getTree($learningPath);

            return new JsonResponse($this->convertTreeToArray($tree));
        }
        catch (Exception $ex)
        {
            return $this->handleException($ex);
        }
    }

    /**
     * @param Tree $tree
     *
     * @return \string[]
     */
    protected function convertTreeToArray(Tree $tree)
    {
        $treeJSONMapper = new TreeJSONMapper(
            $tree, $this->getUser(), null, $this->getAutomaticNumberingService(),
            new NodeBaseActionGenerator(Translation::getInstance(), $this->get_parameters()), '', $tree->getRoot(),
            true, false
        );

        return $treeJSONMapper->getNodes();
    }

    /**
     * @return AutomaticNumberingService | object
     */
    public function getAutomaticNumberingService()
    {
        return $this->getService(AutomaticNumberingService::class);
    }

    /**
     * Returns the LearningPathService service
     *
     * @return LearningPathService | object
     */
    public function getLearningPathService()
    {
        return $this->getService(LearningPathService::class);
    }

    /**
     * @return array
     */
    public function getRequiredPostParameters(): array
    {
        return array(self::PARAM_LEARNING_PATH_ID);
    }
}