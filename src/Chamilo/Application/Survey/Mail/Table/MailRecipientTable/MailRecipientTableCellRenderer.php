<?php
namespace Chamilo\Application\Survey\Mail\Table\MailRecipientTable;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Platform\Translation;

class MailRecipientTableCellRenderer extends DataClassTableCellRenderer
{
    
    // Inherited
    function render_cell($column, $user_mail)
    {
        $user_id = $user_mail->get_user_id();
        $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(User :: class_name(), $user_id);
        
        switch ($column->get_name())
        {
            case User :: PROPERTY_USERNAME :
                return $user->get_fullname();
                break;
            case User :: PROPERTY_EMAIL :
                return $user->get_email();
                break;
            case User :: PROPERTY_FIRSTNAME :
                return $user->get_firstname();
                break;
            case User :: PROPERTY_LASTNAME :
                return $user->get_lastname();
                break;
        }
        
        return parent :: render_cell($column, $user_mail);
    }

    private function get_date($date)
    {
        if ($date == 0)
        {
            return Translation :: get('MailNotSent');
        }
        else
        {
            return date("Y-m-d H:i", $date);
        }
    }
}
?>