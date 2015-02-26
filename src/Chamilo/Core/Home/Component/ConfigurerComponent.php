<?php
namespace Chamilo\Core\Home\Component;

use Chamilo\Core\Home\Form\BlockConfigurationForm;
use Chamilo\Core\Home\Manager;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: configurer.class.php 227 2009-11-13 14:45:05Z kariboe $
 *
 * @package home.lib.home_manager.component
 */
/**
 * Repository manager component to edit an existing learning object.
 */
class ConfigurerComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $id = Request :: get(self :: PARAM_HOME_ID);

        if ($id)
        {
            $url = $this->get_url();

            $object = $this->retrieve_home_block($id);

            if ($object->is_configurable())
            {
                $form = new BlockConfigurationForm($object, $url);

                if ($form->validate())
                {
                    $success = $form->update_block_config();
                    $this->redirect($success);
                }
                else
                {
                    $html = array();

                    $html[] = $this->render_header();
                    $html[] = $form->toHtml();
                    $html[] = $this->render_footer();

                    return implode("\n", $html);
                }
            }
            else
            {
                $html = array();

                $html[] = $this->render_header();
                $html[] = $this->display_warning_message(Translation :: get('NothingToConfigure'));
                $html[] = $this->render_footer();

                return implode("\n", $html);
            }
        }
        else
        {
            return $this->display_error_page(htmlentities(Translation :: get('NoBlockSelected')));
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('home_configurer');
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_HOME_ID);
    }
}
