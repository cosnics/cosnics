<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Mail\Mailer\MailerInterface;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Translation\Translation;
use Exception;

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
    public function run()
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

        $emailAddresses = [];

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
            $mail = new Mail(
                $translator->getTranslation('IncompleteProgressMailTitle', $this->getParameters()), $mailContent,
                $emailAddresses, true, [], [], $this->getUser()->get_fullname(), $this->getUser()->get_email()
            );

            $mailer = $this->getActiveMailer();
            $mailer->sendMail($mail);

            $success = true;
            $message = 'IncompleteProgressMailSent';
        }
        catch (Exception $ex)
        {
            $success = false;
            $message = 'IncompleteProgressMailNotSent';
        }

        $this->redirectWithMessage(
            $translator->getTranslation($message), !$success, [self::PARAM_ACTION => self::ACTION_VIEW_USER_PROGRESS]
        );
    }

    protected function getActiveMailer(): MailerInterface
    {
        return $this->getService('Chamilo\Libraries\Mail\Mailer\ActiveMailer');
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
            $this->getSystemPathBuilder()->getResourcesPath(
                'Chamilo\Core\Repository\ContentObject\LearningPath\Display'
            ) . 'Templates/Mail/IncompleteProgressMail.' . $language . '.html'
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

        $variables = [
            'LEARNING_PATH' => $this->learningPath->get_title(),
            'STEP_NAME' => $currentNodeTitle,
            'USER' => $this->getUser()->get_fullname(),
            'URL' => $this->get_url([self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT])
        ];

        return $variables;
    }

}