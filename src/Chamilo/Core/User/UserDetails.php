<?php
namespace Chamilo\Core\User;

use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class UserDetails
{

    /**
     * The user
     */
    private $user;

    /**
     * Indicates if a border should be included
     */
    private $border;

    /**
     * Constructor
     * 
     * @param $user User
     * @param $border boolean Indicates if a border should be included
     */
    public function __construct($user, $border = true)
    {
        $this->user = $user;
        $this->border = $border;
    }

    /**
     * Returns a HTML representation of the user details
     * 
     * @return string
     * @todo Implement further details
     */
    public function toHtml()
    {
        $html[] = '<div ';
        
        if ($this->border)
        {
            $html[] = 'class="user_details"';
        }
        else
        {
            $html[] = 'class="vertical_space"';
        }
        
        $html[] = 'style="clear: both;background-image: url(' . Theme :: getInstance()->getImagePath() .
             'logo/22.png);">';
        $html[] = '<img src="' . $this->user->get_full_picture_url() . '" alt="' . $this->user->get_fullname() .
             '" style="margin: 10px;max-height: 150px; border:1px solid black;float: right; display: inline;"/>';
        $html[] = '<div class="title">';
        $html[] = $this->user->get_fullname();
        $html[] = '</div>';
        $html[] = '<div class="description">';
        $html[] = Translation :: get('Email') . ': ' . Display :: encrypted_mailto_link($this->user->get_email());
        $html[] = '<br />' . Translation :: get('Username') . ': ' . $this->user->get_username();
        $html[] = '<br />' . Translation :: get('Status') . ': ' .
             ($this->user->get_status() == 1 ? Translation :: get('Teacher') : Translation :: get('Student'));
        
        if ($this->user->is_platform_admin())
        {
            $html[] = ', ' . Translation :: get('PlatformAdministrator');
        }
        
        $html[] = '</div>';
        $html[] = '<div style="clear:both;"><span></span></div>';
        $html[] = '</div>';
        return implode("\n", $html);
    }
}
