<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Form;

use Chamilo\Core\Repository\ContentObject\Rubric\Service\RubricService;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass\Rubric;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CategoryNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\ClusterNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Level;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Translation\Translation;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_Environment;

/**
 *
 * @package repository.lib.content_object.rubric
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */

/**
 * A form to create/update a rubric
 */
class RubricForm extends ContentObjectForm
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject|\Chamilo\Libraries\Architecture\Interfaces\AttachmentSupport|mixed|void|null
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     * @throws \Exception
     */
    public function create_content_object()
    {
        $rubricObject = new Rubric();
        $this->set_content_object($rubricObject);

        parent::create_content_object();

        $rubricData = new RubricData($rubricObject->get_title());
        $rubricData->setContentObjectId($rubricObject->getId());

        $clusterNode = new ClusterNode($rubricObject->get_title(), $rubricData, $rubricData->getRootNode());
        new CategoryNode('', $rubricData, $clusterNode);

        $level = new Level($rubricData);

        $level->setTitle(
            Translation::getInstance()->getTranslation('LevelGood', [], 'Chamilo\Core\Repository\ContentObject\Rubric')
        );

        $level->setScore(10);

        $level2 = new Level($rubricData);

        $level2->setTitle(
            Translation::getInstance()->getTranslation('LevelBad', [], 'Chamilo\Core\Repository\ContentObject\Rubric')
        );

        $level2->setScore(0);

        $this->getRubricService()->saveRubric($rubricData);

        $rubricObject->setActiveRubricDataId($rubricData->getId());
        $rubricObject->update();

        return $rubricObject;
    }

    public function build_creation_form($htmleditor_options = array(), $in_tab = false)
    {
        parent::build_creation_form($htmleditor_options, $in_tab);
    }

    /**
     * @return RubricService|object
     */
    protected function getRubricService()
    {
        return $this->getContainer()->get(RubricService::class);
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        if (!isset($this->container))
        {
            $this->container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
        }

        return $this->container;
    }
}
