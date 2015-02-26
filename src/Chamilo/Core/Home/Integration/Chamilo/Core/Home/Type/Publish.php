<?php
namespace Chamilo\Core\Home\Integration\Chamilo\Core\Home\Type;

use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;

/**
 * A "Static" block.
 * I.e. a block that display the title and description of an object. Usefull to display free html
 * text.
 * 
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 * @package home.block
 */
class Publish extends \Chamilo\Core\Home\BlockRendition
{

    public function display_content()
    {
        $user = $this->get_user();
        $sid = $user ? $user->get_security_token() : 0;
        $parameters = array('view_type' => 'my_chamilo_os', 'timestamp' => time(), 'sid' => $sid);
        $url = $this->get_url($parameters);
        $module_path = Theme :: getInstance()->getImagePath() . 'os_module.png';
        
        $protocol = Request :: server('SERVER_PROTOCOL') == 'HTTPS/1.1' ? 'https://' : 'http://';
        $server = Request :: server('HTTP_HOST');
        $url = $protocol . $server . $url;
        $url_encoded = urlencode($url);
        $url = htmlentities($url);
        // Request::
        
        return <<<EOT
        <a href="http://fusion.google.com/add?source=atgs&amp;moduleurl={$url_encoded}">
            <img src="http://buttons.googlesyndication.com/fusion/add.gif" border="0" alt="Add to Google" />
        </a>
        <a href="{$url}">
            <img src="$module_path" border="0" alt="Module" width="24" />
        </a>
EOT;
    }

    public function is_visible()
    {
        return true; // i.e.display on homepage when anonymous
    }
}
