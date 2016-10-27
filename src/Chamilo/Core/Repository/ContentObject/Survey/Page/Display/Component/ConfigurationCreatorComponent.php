<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Form\ConfigureQuestionForm;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ConfigurationCreatorComponent extends TabComponent
{

    function build()
    {
        if ($this->get_parent()->is_allowed_to_edit_content_object($this->get_current_node()))
        {

            $configuration_id = Request :: get(self :: PARAM_CONFIGURATION_ID);
            $page = $this->get_root_content_object();

            if ($configuration_id)
            {
                $form = new ConfigureQuestionForm($this, $page, $configuration_id);
            }
            else
            {
                $form = new ConfigureQuestionForm($this, $page);
            }

            if ($form->validate())
            {
                $succes = $form->create_configuration();

                if ($succes)
                {
                    $content_object = $this->get_current_content_object();

                    $configuration = $form->getConfiguration();
                    $variable = $configuration_id ? 'configurationUpdated' : 'configurationCreated';
                    $content = Translation :: getInstance()->getTranslation($variable) . ' : ' .
                         $configuration->getName();

                    Event :: trigger(
                        'Activity',
                        \Chamilo\Core\Repository\Manager :: context(),
                        array(
                            Activity :: PROPERTY_TYPE => Activity :: ACTIVITY_UPDATED,
                            Activity :: PROPERTY_USER_ID => $this->get_user_id(),
                            Activity :: PROPERTY_DATE => time(),
                            Activity :: PROPERTY_CONTENT_OBJECT_ID => $content_object->get_id(),
                            Activity :: PROPERTY_CONTENT => $content_object->get_title()));
                }

                $message = htmlentities(
                    Translation :: get(
                        ($succes ? 'ObjectUpdated' : 'ObjectNotUpdated'),
                        array('OBJECT' => Translation :: get('ContentObject')),
                        Utilities :: COMMON_LIBRARIES));

                $params = array();
                $params[self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
                $params[self :: PARAM_ACTION] = self :: ACTION_QUESTION_MANAGER;

                $this->redirect($message, (! $succes), $params);
            }
            else
            {
                $html = array();

                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            throw new NotAllowedException();
        }
    }

    /**
     *
     * @see \libraries\SubManager::get_additional_parameters()
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_STEP, self :: PARAM_CONFIGURATION_ID);
    }
}

?>