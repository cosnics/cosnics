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

        $postBackUrl = new Redirect(
            array(
                RepositoryManager::PARAM_CONTEXT => RepositoryManager::package(),
                RepositoryManager::PARAM_ACTION => RepositoryManager::ACTION_QUOTA,
                QuotaManager::PARAM_ACTION => QuotaManager::ACTION_RIGHTS,
                Manager::PARAM_ACTION => Manager::ACTION_CREATE
            )
        );

        $rightsForm = new RightsGroupForm(
            $postBackUrl->getUrl(), $this->getTranslator(), false, $rightsService->getAvailableRights(),
            $rightsService->getAvailableEntities()
        );

        if ($rightsForm->validate())
        {
            $success = $rightsService->setRightsConfigurationForUserFromValues(
                $this->getUser(), $rightsForm->exportValues()
            );

            $message = $this->getTranslator()->trans(
                $success ? 'RightsConfigured' : 'RightsNotConfigured',
                array('OBJECT' => $this->getTranslator()->trans('Quota', [], 'Chamilo\Core\Repository\Quota\Rights')),
                'Chamilo\Libraries\Rights'
            );

            $postBackUrl->toUrl();
        }

        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->getLinkTabsRenderer()->render($this->get_tabs(self::ACTION_CREATE), $rightsForm->render());
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }
}
