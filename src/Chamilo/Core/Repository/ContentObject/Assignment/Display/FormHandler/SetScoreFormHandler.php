<?php
/**
 * Created by PhpStorm.
 * User: pjbro
 * Date: 19/04/18
 * Time: 10:40
 */

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\FormHandler;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\ScoreFormProcessor;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\ScoreService;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
use Chamilo\Core\User\Storage\DataClass\User;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class SetScoreFormHandler
{
    /**
     * @var ScoreService
     */
    protected $scoreService;

    /**
     * SetScoreFormHandler constructor.
     * @param ScoreService $scoreService
     */
    public function __construct(ScoreService $scoreService)
    {
        $this->scoreService = $scoreService;
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

        $this->scoreService->createOrUpdateScore($score);

        return true;
    }

}