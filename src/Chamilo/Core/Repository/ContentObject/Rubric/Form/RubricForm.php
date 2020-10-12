<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Form;

use Chamilo\Core\Repository\ContentObject\Rubric\Service\RubricService;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass\Rubric;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CategoryNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\ClusterNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Level;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
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

    public function __construct(
        $form_type, WorkspaceInterface $workspace, $content_object, $form_name, $method = 'post',
        $action = null, $extra = null, $additional_elements
    ) {
        // allow_new_version: false
        parent::__construct($form_type, $workspace, $content_object, $form_name, $method, $action, $extra, $additional_elements, false);
    }

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

        $values = $this->exportValues();
        $useScores = (bool) $values[Rubric::PROPERTY_RUBRIC_USE_SCORES];
        $rubricData = new RubricData($rubricObject->get_title());
        $rubricData->setContentObjectId($rubricObject->getId());
        $rubricData->setUseScores($useScores);

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

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject|\Chamilo\Libraries\Architecture\Interfaces\AttachmentSupport|mixed|void|null
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\RubricStructureException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    public function update_content_object()
    {
        $rubricData = $this->getRubricService()->getRubric($this->get_content_object()->get_additional_properties()['active_rubric_data_id']);
        if ($this->getRubricService()->canChangeRubric($rubricData)) {
            $values = $this->exportValues();
            $useScores = (bool) $values[Rubric::PROPERTY_RUBRIC_USE_SCORES];
            $rubricData->setUseScores($useScores);
            $this->getRubricService()->saveRubric($rubricData);
        }
        return parent::update_content_object();
    }

    /**
     * @param array $htmleditor_options
     * @param bool $in_tab
     */
    public function build_creation_form($htmleditor_options = array(), $in_tab = false)
    {
        parent::build_creation_form($htmleditor_options, $in_tab);
        $this->buildRubricForm();
    }

    /**
     * @param array $htmleditor_options
     * @param bool $in_tab
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     * @throws \Doctrine\ORM\ORMException
     */
    public function build_editing_form($htmleditor_options = array(), $in_tab = false)
    {
        parent::build_editing_form($htmleditor_options, $in_tab);
        $rubricData = $this->getRubricService()->getRubric($this->get_content_object()->get_additional_properties()['active_rubric_data_id']);
        $this->buildRubricForm($rubricData->useScores(), !$this->getRubricService()->canChangeRubric($rubricData));
    }

    /**
     * Builds the form for the additional rubric properties
     * @param bool $use_scores
     */
    protected function buildRubricForm($use_scores = false, $has_results = false)
    {
        $translator = Translation::getInstance();
        $this->addElement('category', $translator->getTranslation('Properties'));
        $el = $this->addElement(
            'checkbox', Rubric::PROPERTY_RUBRIC_USE_SCORES,
            $translator->getTranslation('RubricUseScores')
        );
        $el->setChecked($use_scores);
        if ($has_results) {
            $el->setAttribute('disabled', true);
            $this->add_warning_message('', '', $translator->getTranslation('RubricUseScoresDisabled'));
        }
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
