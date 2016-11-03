<?php
namespace Chamilo\Core\Admin\Language\Component;

use Chamilo\Core\Admin\Language\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class BrowserComponent extends Manager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $types = array(self :: ACTION_IMPORT, self :: ACTION_EXPORT);

        $html = array();

        $html[] = $this->render_header();

        foreach ($types as $type)
        {
            $html[] = '<a href="' . $this->get_url(array(self :: PARAM_ACTION => $type)) . '">';
            $html[] = '<div class="create_block" style="background-image: url(' . Theme :: getInstance()->getImagePath(
                'Chamilo\Core\Admin\Language',
                'Component/' . $type) . ');">';
            $html[] = Translation :: get(
                (string) StringUtilities :: getInstance()->createString($type)->upperCamelize() . 'Component');
            $html[] = '</div>';
            $html[] = '</a>';
        }

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }
}
