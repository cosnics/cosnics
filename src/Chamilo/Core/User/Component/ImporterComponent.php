<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Form\UserImportForm;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserImporter\UserImporter;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * @package Chamilo\Core\User\Component
 */
class ImporterComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \QuickformException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageUsers');

        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $form = new UserImportForm($this->get_url());

        if ($form->validate())
        {
            $uploadedFile = $this->getRequest()->files->get('file');
            $userImporterResult = $this->getUserImporter()->importUsersFromFile(
                $this->getUser(), $uploadedFile, boolval($form->exportValues()['mail']['send_mail'])
            );

            $rendition = $this->getTwig()->render(
                'Chamilo\Core\User:UserImporterResult.html.twig', ['userImporterResult' => $userImporterResult]
            );
        }
        else
        {
            $emailRequired = $this->getConfigurationConsulter()->getSetting([Manager::CONTEXT, 'require_email']);
            $rendition = $this->getTwig()->render(
                'Chamilo\Core\User:UserImporter.html.twig',
                ['emailRequired' => $emailRequired, 'form' => $form->render()]
            );
        }

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $rendition;
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    protected function getUserImporter(): UserImporter
    {
        return $this->getService(UserImporter::class);
    }
}
