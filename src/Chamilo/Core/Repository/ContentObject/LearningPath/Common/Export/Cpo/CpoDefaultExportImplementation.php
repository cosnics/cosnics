<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Common\Export\Cpo;

use Chamilo\Core\Repository\Common\Export\ContentObjectExport;
use Chamilo\Core\Repository\ContentObject\LearningPath\Common\Export\CpoExportImplementation;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathChildService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPathChild;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;

class CpoDefaultExportImplementation extends CpoExportImplementation
{

    /**
     * Renders the export
     */
    public function render()
    {
        ContentObjectExport::launch($this);

        $document = $this->get_context()->get_dom_document();
        $contentObject = $this->get_content_object();
        $contentObjectNode = $this->get_context()->get_content_object_node($contentObject->get_id());

        if ($contentObject instanceof LearningPath)
        {
            $this->exportLearningPathChildren($contentObject, $contentObjectNode, $document);
        }
    }

    /**
     * Exports the learning path child objects for a given learning path
     *
     * @param LearningPath $learningPath
     * @param \DOMElement $contentObjectNode
     * @param \DOMDocument $document
     */
    protected function exportLearningPathChildren(
        LearningPath $learningPath, \DOMElement $contentObjectNode, \DOMDocument $document
    )

    {
        $childrenNode = $document->createElement('children');
        $contentObjectNode->appendChild($childrenNode);

        $learningPathChildService = $this->getLearningPathChildService();
        $contentObjectRepository = $this->getContentObjectRepository();

        $learningPathChildren = $learningPathChildService->getLearningPathChildrenForLearningPath($learningPath);

        foreach($learningPathChildren as $learningPathChild)
        {
            try
            {
                $contentObject = $contentObjectRepository->findById($learningPathChild->getContentObjectId());
                if ($contentObject instanceof ContentObject)
                {
                    if(!$this->get_context()->in_id_cache($contentObject->getId()))
                    {
                        $this->get_context()->process($contentObject);
                    }
                }
            }
            catch(\Exception $ex)
            {
                continue;
            }

            $learningPathChildNode = $document->createElement('child');

            foreach($learningPathChild->get_default_properties() as $propertyName => $propertyValue)
            {
                $propertyNode = $document->createElement($propertyName);
                $propertyNode->appendChild($document->createTextNode($propertyValue));

                $learningPathChildNode->appendChild($propertyNode);
            }

            $childrenNode->appendChild($learningPathChildNode);
        }
    }

    /**
     * @return object | LearningPathChildService
     */
    protected function getLearningPathChildService()
    {
        $serviceContainer = DependencyInjectionContainerBuilder::getInstance()->createContainer();

        return $serviceContainer->get(
            'chamilo.core.repository.content_object.learning_path.service.learning_path_child_service'
        );
    }

    /**
     * @return object | ContentObjectRepository
     */
    protected function getContentObjectRepository()
    {
        $serviceContainer = DependencyInjectionContainerBuilder::getInstance()->createContainer();

        return $serviceContainer->get(
            'chamilo.core.repository.workspace.repository.content_object_repository'
        );
    }
}
