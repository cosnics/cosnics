<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Form\UserImportForm;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserImporter\ImportParser\ImportParserFactory;
use Chamilo\Core\User\Service\UserImporter\UserImporter;
use Chamilo\Core\User\Storage\Repository\UserRepository;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Hashing\HashingUtilities;
use Chamilo\Libraries\Mail\Mailer\MailerFactory;
use Chamilo\Libraries\Mail\Mailer\MailerInterface;

/**
 * $Id: importer.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 *
 * @package user.lib.user_manager.component
 */
class ImporterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageUsers');

        if (! $this->getUser()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $form = new UserImportForm(UserImportForm::TYPE_IMPORT, $this->get_url(), $this->get_user());

        if ($form->validate())
        {
            $userImporter = new UserImporter(
                new ImportParserFactory(),
                new UserRepository(),
                $this->getConfigurationConsulter(),
                $this->getHashingUtilities(),
                $this->getMailer(),
                $this->getTranslator());

            $uploadedFile = $this->getRequest()->files->get('file');
            $userImporterResult = $userImporter->importUsersFromFile(
                $this->getUser(),
                $uploadedFile,
                boolval($form->exportValues()['mail']['send_mail']));

            $rendition = $this->getTwig()->render(
                'Chamilo\Core\User:UserImporterResult.html.twig',
                ['userImporterResult' => $userImporterResult]);
        }
        else
        {
            $emailRequired = Configuration::getInstance()->get_setting(array(Manager::context(), 'require_email'));
            $rendition = $this->getTwig()->render(
                'Chamilo\Core\User:UserImporter.html.twig',
                ['emailRequired' => $emailRequired, 'form' => $form->toHtml()]);
        }

        $html = array();

        $html[] = $this->render_header();
        $html[] = $rendition;
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return HashingUtilities | object
     */
    protected function getHashingUtilities()
    {
        return $this->getService('chamilo.libraries.hashing.hashing_utilities');
    }

    /**
     *
     * @return MailerInterface
     */
    protected function getMailer()
    {
        $factory = new MailerFactory();
        return $factory->getActiveMailer();
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('user_importer');
    }
}
