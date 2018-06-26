<?php
/**
 * Created by PhpStorm.
 * User: pjbro
 * Date: 19/04/18
 * Time: 10:40
 */

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\FormHandler;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\ScoreService;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\FormHandler;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class SetScoreFormHandler extends FormHandler
{

    /**
     * @var ScoreService
     */
    protected $scoreService;

    /**
     * @var User $user
     */
    protected $user;

    /**
     * SetScoreFormHandler constructor.
     * @param ScoreService $scoreService
     */
    public function __construct(ScoreService $scoreService)
    {
        $this->scoreService = $scoreService;
    }

    /**
     * @param User $user
     */
    public function setScoringUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param FormInterface $form
     * @param Request $request
     * @return bool
     * @throws \Exception
     */
    public function handle(FormInterface $form, Request $request) : bool
    {
        if(empty($this->user)) {
            throw new \Exception('User must be defined. Use setter');
        }

        if(parent::handle($form, $request)) {
            $this->scoreService->createOrUpdateScoreByUser($form->getData(), $this->user);

            return true;
        }

        return false;
    }

    /**
     * @param FormInterface $form
     */
    protected function rollBackModel(FormInterface $form)
    {
        /**
         * @var Score $score
         */
        $score = $form->getData();
        $score->setScore($this->originalModel->getScore());
    }
}