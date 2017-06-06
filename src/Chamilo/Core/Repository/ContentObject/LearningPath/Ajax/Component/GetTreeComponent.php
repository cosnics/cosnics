<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Renderer\TreeJSONMapper;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActionGenerator\NodeBaseActionGenerator;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AutomaticNumberingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeBuilder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Platform\Translation;
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

            if(!$this->getRightsService()->canCopyContentObject($this->getUser(), $learningPath))
            {
                throw new NotAllowedException();
            }

            $tree = $this->getTreeBuilder()->buildTree($learningPath);

            return new JsonResponse($this->convertTreeToArray($tree));
        }
        catch (\Exception $ex)
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
            $tree, $this->getUser(),
            null,
            $this->getAutomaticNumberingService(),
            new NodeBaseActionGenerator(Translation::getInstance(), $this->get_parameters()),
            '',
            $tree->getRoot(), true, false
        );

        return $treeJSONMapper->getNodes();
    }

    /**
     * @return array
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_LEARNING_PATH_ID);
    }

    /**
     * Returns the TreeBuilder service
     *
     * @return TreeBuilder | object
     */
    public function getTreeBuilder()
    {
        return $this->getService(
            'chamilo.core.repository.content_object.learning_path.service.tree_builder'
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