<?php
namespace Chamilo\Application\CasUser\Rights\Package;

use Chamilo\Application\CasUser\Rights\Rights;
use Chamilo\Libraries\Platform\Translation;

class Installer extends \Chamilo\Configuration\Package\Action\Installer
{

    public function extra()
    {
        if (! Rights :: get_instance()->create_cas_root())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('QuotaLocationCreated'));
        }

        return true;
    }
}
