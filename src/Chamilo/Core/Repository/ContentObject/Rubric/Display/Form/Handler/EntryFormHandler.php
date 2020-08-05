<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Display\Form\Handler;

use Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Model\CriteriumResultJSONModel;
use Chamilo\Core\Repository\ContentObject\Rubric\Display\Form\EntryFormType;
use Chamilo\Core\Repository\ContentObject\Rubric\Service\RubricResultService;
use Chamilo\Libraries\Format\Form\FormHandler;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;

/**
 * Class EntryFormHandler
 *
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Display\Form\Handler
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class EntryFormHandler extends FormHandler
{
    /**
     * @var RubricResultService
     */
    protected $rubricResultService;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var EntryFormHandlerParameters
     */
    protected $parameters;

    /**
     * EntryFormHandler constructor.
     *
     * @param RubricResultService $rubricResultService
     * @param Serializer $serializer
     */
    public function __construct(RubricResultService $rubricResultService, Serializer $serializer)
    {
        $this->rubricResultService = $rubricResultService;
        $this->serializer = $serializer;
    }

    public function setParameters(EntryFormHandlerParameters $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @param FormInterface $form
     * @param Request $request
     *
     * @return bool
     * @throws \Exception
     */
    public function handle(FormInterface $form, Request $request): bool
    {
        if (!$this->parameters instanceof EntryFormHandlerParameters)
        {
            throw new \InvalidArgumentException('The invite form handler can only work with a valid parameters object');
        }

        if (!parent::handle($form, $request))
        {
            return false;
        }

        $data = $form->getData();

//        $data[EntryFormType::ELEMENT_RUBRIC_RESULTS] = '[{"criterium_tree_node_id": 50, "level_id": 7, "comment": "meh"}]';

        $resultJSONModels = $this->serializer->deserialize(
            $data[EntryFormType::ELEMENT_RUBRIC_RESULTS], 'array<' . CriteriumResultJSONModel::class . '>', 'json'
        );

        foreach ($this->parameters->getTargetUsers() as $targetUser)
        {
            $this->rubricResultService->storeRubricResults(
                $this->parameters->getUser(), $targetUser, $this->parameters->getRubricData(),
                $this->parameters->getContextIdentifier(), $resultJSONModels
            );
        }

        return true;
    }

    protected function rollBackModel(FormInterface $form)
    {
        // TODO: Implement rollBackModel() method.
    }
}
