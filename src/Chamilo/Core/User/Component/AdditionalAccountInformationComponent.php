<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\User\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AdditionalAccountInformationComponent extends ProfileComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageAccount');
        return $this->renderPage();
    }

    /**
     *
     * @return string
     */
    public function getContent()
    {
        $form_executer = new \Chamilo\Configuration\Form\Executer(
            $this, 
            'account_fields', 
            Translation::get('AdditionalUserInformation'));
        return $form_executer->run();
    }
}
