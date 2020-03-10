<?php
namespace Chamilo\Core\Repository\ContentObject\Rubric\Common\Export\Cpo;

use Chamilo\Core\Repository\Common\Export\ContentObjectExport;
use Chamilo\Core\Repository\Common\Export\Cpo\CpoContentObjectExportController;
use Chamilo\Core\Repository\ContentObject\LearningPath\Common\Export\CpoExportImplementation;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\Rubric\Service\RubricService;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass\Rubric;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\ClusterNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CriteriumNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Level;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;

class CpoDefaultExportImplementation extends CpoExportImplementation
{
    use DependencyInjectionContainerTrait;

    /**
     * Renders the export
     */
    public function render()
    {
        $this->initializeContainer();

        ContentObjectExport::launch($this);

        /** @var CpoContentObjectExportController $context */
        $context = $this->get_context();
        $document = $context->get_dom_document();
        $contentObject = $this->get_content_object();
        $contentObjectNode = $context->get_content_object_node($contentObject->get_id());

        if ($contentObject instanceof Rubric)
        {
            $this->exportRubricData($contentObject, $contentObjectNode, $document);
        }
    }

    /**
     * @param Rubric $rubric
     * @param \DOMElement $contentObjectNode
     * @param \DOMDocument $document
     *
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     * @throws \Doctrine\ORM\ORMException
     */
    protected function exportRubricData(Rubric $rubric, \DOMElement $contentObjectNode, \DOMDocument $document)
    {
        $rubricData = $this->getRubricService()->getRubric($rubric->getActiveRubricDataId());

        $node1 = new ClusterNode('test cluster', $rubricData);
        $node1->setId(2);

        $node2 = new ClusterNode('test cluster 2', $rubricData);
        $node2->setId(3);

        $node3 = new CriteriumNode('test criterium 1', $rubricData);
        $node3->setId(4);

        $rubricData->getRootNode()->addChild($node1)->addChild($node2)->addChild($node3);

        $level = new Level($rubricData);
        $level->setId(1);
        $level->setTitle('Good');

        $jsonFormat = $this->getSerializer()->serialize($rubricData, 'json');

        $rubricDataNode = $document->createElement('rubric-data');
        $rubricDataNode->appendChild($document->createTextNode($jsonFormat));

        $contentObjectNode->appendChild($rubricDataNode);
    }

    /**
     * @return RubricService
     */
    public function getRubricService()
    {
        return $this->getService(RubricService::class);
    }

}
