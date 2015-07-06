<?php
namespace Chamilo\Application\Survey\Package;

use Chamilo\Application\Survey\Rights\Rights;
use Chamilo\Libraries\Platform\Translation;
/**
 * $Id: survey_installer.class.php 201 2009-11-13 12:34:51Z chellee $
 * 
 * @package application.survey.install
 */

/**
 * This installer can be used to create the storage structure for the personal calendar application.
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{

//     public function extra()
//     {
//         if (! Rights :: get_instance()->create_application_root())
//         {
//             return false;
//         }
//         else
//         {
//             $this->add_message(self :: TYPE_NORMAL, Translation :: get('ApplicationRootLocationCreated'));
//         }
        
//         if (! Rights :: get_instance()->create_publication_root())
//         {
//             return false;
//         }
//         else
//         {
//             $this->add_message(self :: TYPE_NORMAL, Translation :: get('PublicationRootLocationCreated'));
//         }
    
//         return true;
//     }
    
}
