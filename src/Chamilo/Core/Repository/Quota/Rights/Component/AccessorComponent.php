<?php
namespace Chamilo\Core\Repository\Quota\Rights\Component;

use Chamilo\Core\Repository\Manager as RepositoryManager;
use Chamilo\Core\Repository\Quota\Manager as QuotaManager;
use Chamilo\Core\Repository\Quota\Rights\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Rights\Form\RightsForm;

/**
 * @package Chamilo\Core\Repository\Quota\Rights\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
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

        $urlParameters = [
            RepositoryManager::PARAM_CONTEXT => RepositoryManager::CONTEXT,
            RepositoryManager::PARAM_ACTION => RepositoryManager::ACTION_QUOTA,
            QuotaManager::PARAM_ACTION => QuotaManager::ACTION_RIGHTS,
            Manager::PARAM_ACTION => Manager::ACTION_ACCESS
        ];

        $postBackUrl = $this->getUrlGenerator()->fromParameters($urlParameters);

        $rightsForm = new RightsForm(
            $postBackUrl, false, $rightsService->getAvailableRights(), $rightsService->getAvailableEntities()
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
                ['OBJECT' => $this->getTranslator()->trans('Quota', [], 'Chamilo\Core\Repository\Quota\Rights')],
                'Chamilo\Libraries\Rights'
            );

            $this->redirectWithMessage($message, !$success, $urlParameters);
        }

        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->getLinkTabsRenderer()->render($this->get_tabs(self::ACTION_ACCESS), $rightsForm->render());
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }
}
