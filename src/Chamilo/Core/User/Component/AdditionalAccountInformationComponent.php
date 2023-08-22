<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Configuration\Form\Executer;
use Chamilo\Core\User\Manager;

/**
 * @package Chamilo\Core\User\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class AdditionalAccountInformationComponent extends ProfileComponent
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function run()
    {
        $this->checkAuthorization(Manager::CONTEXT, 'ManageAccount');

        return $this->renderPage();
    }

    /**
     * @throws \QuickformException
     */
    public function getContent(): string
    {
        return $this->getExecuter()->run(
            $this, 'account_fields', $this->getTranslator()->trans('AdditionalUserInformation', [], Manager::CONTEXT)
        );
    }

    public function getExecuter(): Executer
    {
        return $this->getService(Executer::class);
    }
}
