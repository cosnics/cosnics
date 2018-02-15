<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Mail\Mailer\MailerFactory;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Translation\Translation;

/**
 * Mails the users that do not have completed the given TreeNode
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserIncompleteProgressMailerComponent extends Manager
{
    /**
     * Runs this component and returns its output
     *
     * @throws NotAllowedException
     */
    function run()
    {
        if (!$this->canEditCurrentTreeNode())
        {
            throw new NotAllowedException();
        }

        $currentTreeNode = $this->getCurrentTreeNode();
        $trackingService = $this->getTrackingService();

        $usersNotYetStarted = $trackingService->findTargetUsersWithoutLearningPathAttempts(
            $this->learningPath, $currentTreeNode
        );

        $usersPartiallyStarted = $trackingService->findTargetUsersWithPartialLearningPathAttempts(
            $this->learningPath, $currentTreeNode
        );

        $emailAddresses = array();

        $emailAddresses[] = $this->getUser()->get_email();

        foreach ($usersNotYetStarted as $userNotYetStarted)
        {
            $emailAddresses[] = $userNotYetStarted[User::PROPERTY_EMAIL];
        }

        foreach ($usersPartiallyStarted as $userPartiallyStarted)
        {
            $emailAddresses[] = $userPartiallyStarted[User::PROPERTY_EMAIL];
        }

        $mailContent = $this->getMailContent();

        $translator = Translation::getInstance();

        try
        {
            $mailerFactory = new MailerFactory();
            $mail = new Mail(
                $translator->getTranslation('IncompleteProgressMailTitle', $this->getParameters()),
                $mailContent, $emailAddresses, true, array(), array(), $this->getUser()->get_fullname(),
                $this->getUser()->get_email()
            );

            $mailer = $mailerFactory->getActiveMailer();
            $mailer->sendMail($mail);

            $success = true;
            $message = 'IncompleteProgressMailSent';
        }
        catch (\Exception $ex)
        {
            $success = false;
            $message = 'IncompleteProgressMailNotSent';
        }

        $this->redirect(
            $translator->getTranslation($message), !$success,
            array(self::PARAM_ACTION => self::ACTION_VIEW_USER_PROGRESS)
        );
    }

    /**
     * @return string
     */
    protected function getMailContent()
    {
        $translator = Translation::getInstance();
        $language = $translator->getLanguageIsocode();

        $variables = $this->getParameters();

        $contents = file_get_contents(
            $this->getPathBuilder()->getResourcesPath('Chamilo\Core\Repository\ContentObject\LearningPath\Display') .
            'Templates/Mail/IncompleteProgressMail.' . $language . '.html'
        );

        foreach ($variables as $variable => $value)
        {
            $contents = str_replace('{' . $variable . '}', $value, $contents);
        }

        return $contents;
    }

    /**
     * @return array
     */
    protected function getParameters(): array
    {
        $automaticNumberingService = $this->getAutomaticNumberingService();
        $currentNodeTitle = $automaticNumberingService->getAutomaticNumberedTitleForTreeNode(
            $this->getCurrentTreeNode()
        );

        $variables = array(
            'LEARNING_PATH' => $this->learningPath->get_title(),
            'STEP_NAME' => $currentNodeTitle,
            'USER' => $this->getUser()->get_fullname(),
            'URL' => $this->get_url(array(self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT))
        );

        return $variables;
    }

}