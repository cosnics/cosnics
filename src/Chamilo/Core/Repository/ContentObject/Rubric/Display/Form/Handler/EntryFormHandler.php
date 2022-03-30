<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Display\Form\Handler;

use Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Model\TreeNodeResultJSONModel;
use Chamilo\Core\Repository\ContentObject\Rubric\Display\Form\EntryFormType;
use Chamilo\Core\Repository\ContentObject\Rubric\Service\RubricResultService;
use Chamilo\Core\Repository\ContentObject\Rubric\Service\RubricService;
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
     * @param RubricService $rubricService
     * @param Serializer $serializer
     */
    public function __construct(
        RubricResultService $rubricResultService, Serializer $serializer
    )
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

        $resultJSONModels = $this->serializer->deserialize(
            $data[EntryFormType::ELEMENT_RUBRIC_RESULTS], 'array<' . TreeNodeResultJSONModel::class . '>', 'json'
        );

        $currentTime = new \DateTime();

        $totalScore = $this->rubricResultService->storeRubricResults(
            $this->parameters->getUser(), $this->parameters->getTargetUsers(), $this->parameters->getRubricData(),
            $this->parameters->getContextIdentifier(), $resultJSONModels, $currentTime
        );

        if ($totalScore !== null && $this->parameters->getRubricData()->useScores())
        {
            $this->parameters->getRubricBridge()->saveScore(
                $this->parameters->getUser(), $totalScore,
                $this->parameters->getRubricData()->getMaximumScore()
            );
        }

        return true;
    }

    protected function rollBackModel(FormInterface $form)
    {
        // TODO: Implement rollBackModel() method.
    }
}
