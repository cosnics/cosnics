<?php
namespace Chamilo\Core\Repository\Quota\Rights\Component;

use Chamilo\Core\Repository\Quota\Rights\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Rights\Form\RightsForm;
use Chamilo\Core\Repository\Manager as RepositoryManager;
use Chamilo\Core\Repository\Quota\Manager as QuotaManager;

/**
 * @package Chamilo\Core\Repository\Quota\Rights\Component
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AccessorComponent extends Manager
{

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Exception
     */
    public function run()
    {
        if (!$this->getRightsService()->canUserSetRightsForQuotaRequests($this->getUser()))
        {
            throw new NotAllowedException();
        }

        $rightsService = $this->getRightsService();

        $urlParameters = array(
            RepositoryManager::PARAM_CONTEXT => RepositoryManager::package(),
            RepositoryManager::PARAM_ACTION => RepositoryManager::ACTION_QUOTA,
            QuotaManager::PARAM_ACTION => QuotaManager::ACTION_RIGHTS, Manager::PARAM_ACTION => Manager::ACTION_ACCESS
        );

        $postBackUrl = new Redirect($urlParameters);

        $rightsForm = new RightsForm(
            $postBackUrl->getUrl(), $this->getTranslator(), false, $rightsService->getAvailableRights(),
            $rightsService->getAvailableEntities()
        );

        $rightsForm->setRightsDefaults(
            $this->getUser(), false, $rightsService->getTargetUsersAndGroupsForAvailableRights()
        );

        if ($rightsForm->validate())
        {
            $success = $rightsService->saveRightsConfigurationForUserFromValues(
                $this->getUser(), $rightsForm->exportValues()
            );

            $message = $this->getTranslator()->trans(
                $success ? 'RightsConfigured' : 'RightsNotConfigured',
                array('OBJECT' => $this->getTranslator()->trans('Quota', [], 'Chamilo\Core\Repository\Quota\Rights')),
                'Chamilo\Libraries\Rights'
            );

            $this->redirect($message, !$success, $urlParameters);
        }

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->get_tabs(self::ACTION_ACCESS, $rightsForm->render())->render();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }
}
