<?php
namespace Chamilo\Application\Survey\Component;

use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;

/**
 *
 * @package Chamilo\Application\Survey\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class MailerComponent extends TabComponent
{

    public function build()
    {
        $factory = new ApplicationFactory(
            \Chamilo\Application\Survey\Mail\Manager::context(), 
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        return $factory->run();
    }

    public function get_additional_parameters()
    {
        return array(self::PARAM_PUBLICATION_ID);
    }
}