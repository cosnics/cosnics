<?php
/**
 * Created by PhpStorm.
 * User: pjbro
 * Date: 19/04/18
 * Time: 10:40
 */

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\FormHandler;


use Chamilo\Core\Repository\ContentObject\Assignment\Display\Form\ScoreFormType;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\ScoreFormProcessor;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\ScoreService;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
use Chamilo\Core\User\Storage\DataClass\User;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class SetScoreFormHandler
{
    /**
     * @var AssignmentDataProvider
     */
    protected $assignmentDataProvider;
    /**
     * @var ScoreService
     */
    protected $scoreService;

    /**
     * SetScoreFormHandler constructor.
     * @param ScoreService $scoreService
     * @param AssignmentDataProvider $assignmentDataProvider
     */
    public function __construct(ScoreService $scoreService, AssignmentDataProvider $assignmentDataProvider)
    {
        $this->scoreService = $scoreService;
        $this->assignmentDataProvider = $assignmentDataProvider;
    }

    /**
     * @param FormInterface $form
     * @param Request $request
     * @param User $user
     * @param Entry $entry
     * @return bool
     * @throws \Exception
     */
    public function handle(FormInterface $form, Request $request, User $user, Entry $entry) : bool
    {
        if (!$request->isMethod('POST')) {
            return false;
        }

        $form->handleRequest($request);

        if (!$form->isValid()) {
            return false;
        }

        $score = $form->getData();

        $this->assignmentDataProvider->updateScore($score);

        $this->scoreService->createOrUpdateScore($scoreData[ScoreFormType::FIELD_SCORE], $user, $entry);

        return true;
    }

}