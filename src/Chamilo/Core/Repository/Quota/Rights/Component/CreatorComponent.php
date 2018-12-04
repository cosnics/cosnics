<?php
namespace Chamilo\Core\Repository\Quota\Rights\Component;

use Chamilo\Core\Repository\Manager as RepositoryManager;
use Chamilo\Core\Repository\Quota\Manager as QuotaManager;
use Chamilo\Core\Repository\Quota\Rights\Form\RightsGroupForm;
use Chamilo\Core\Repository\Quota\Rights\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Redirect;

class CreatorComponent extends Manager
{

    public function run()
    {
        if (!$this->getUser()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $rightsService = $this->getRightsService();

        $postBackUrl = new Redirect(
            array(
                RepositoryManager::PARAM_CONTEXT => RepositoryManager::package(),
                RepositoryManager::PARAM_ACTION => RepositoryManager::ACTION_QUOTA,
                QuotaManager::PARAM_ACTION => QuotaManager::ACTION_RIGHTS,
                Manager::PARAM_ACTION => Manager::ACTION_ACCESS
            )
        );

        $rightsForm = new RightsGroupForm(
            $postBackUrl->getUrl(), $this->getTranslator(), false, $rightsService->getAvailableRights(),
            $rightsService->getAvailableEntities()
        );

        if ($rightsForm->validate())
        {
            //            $success = $rightsForm->set_rights();

            //            $this->redirect(
            //                Translation::get($success ? 'AccessRightsSaved' : 'AccessRightsNotSaved'), ($success ? false : true)
            //            );
        }

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->get_tabs(self::ACTION_CREATE, $rightsForm->render())->render();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }
}
