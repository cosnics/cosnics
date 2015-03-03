<?php
namespace Chamilo\Application\Survey\Component;

use Chamilo\Application\Survey\Form\PublicationForm;
use Chamilo\Application\Survey\Manager;
use Chamilo\Application\Survey\Rights\Rights;
use Chamilo\Core\Repository\ContentObject\Survey\Storage\DataClass\Survey;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

class PublisherComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        if (! Rights :: get_instance()->publication_is_allowed())
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }

        $html = array();

        if (! \Chamilo\Core\Repository\Viewer\Manager :: is_ready_to_be_published())
        {
            $repository_viewer = \Chamilo\Core\Repository\Viewer\Manager :: construct($this);
            // $repository_viewer->set_maximum_select(RepoViewer :: SELECT_SINGLE);
            $repository_viewer->run();
        }
        else
        {
            $object_ids = \Chamilo\Core\Repository\Viewer\Manager :: get_selected_objects();
            if (! is_array($object_ids))
            {
                $object_ids = array($object_ids);
            }

            if (count($object_ids) > 0)
            {
                $condition = new InCondition(
                    new PropertyConditionVariable(Survey :: class_name(), Survey :: PROPERTY_ID),
                    $object_ids);
                $parameters = new DataClassRetrievesParameters($condition);

                $content_objects = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_active_content_objects(
                    Survey :: class_name(),
                    $parameters);

                $html[] = '<div class="content_object padding_10">';
                $html[] = '<div class="title">' . Translation :: get('SelectedSurvey') . '</div>';
                $html[] = '<div class="description">';
                $html[] = '<ul class="attachments_list">';
                $title = '';
                while ($content_object = $content_objects->next_result())
                {
                    $html[] = '<li><img src="' . Theme :: getInstance()->getImagePath(
                        'Chamilo\Application\Survey',
                        'Logo/22') . '" alt="' .
                         htmlentities(
                            Translation :: get(ContentObject :: type_to_class($content_object->get_type()) . 'TypeName')) .
                         '"/> ' . $content_object->get_title() . '</li>';
                    $title = $content_object->get_title();
                }

                $html[] = '</ul>';
                $html[] = '</div>';
                $html[] = '</div>';
            }

            $parameters = $this->get_parameters();
            $parameters[\Chamilo\Core\Repository\Viewer\Manager :: PARAM_ID] = $object_ids;
            $parameters[\Chamilo\Core\Repository\Viewer\Manager :: PARAM_ACTION] = \Chamilo\Core\Repository\Viewer\Manager :: ACTION_PUBLISHER;

            $form = new PublicationForm(
                PublicationForm :: TYPE_CREATE,
                $object_ids,
                $this->get_user(),
                $this->get_url($parameters),
                null,
                $title);
            if ($form->validate())
            {
                $succes = $form->create_publications();

                if (! $succes)
                {
                    $message = Translation :: get('SurveyNotPublished');
                }
                else
                {
                    $message = Translation :: get('SurveyPublished');
                }

                $this->redirect(
                    $message,
                    (! $succes ? true : false),
                    array(
                        Application :: PARAM_ACTION => self :: ACTION_BROWSE,
                        BrowserComponent :: PARAM_TABLE_TYPE => BrowserComponent :: TAB_MY_PUBLICATIONS));
            }
            else

            {
                $html[] = $form->toHtml();
                $html[] = '<div style="clear: both;"></div>';

                $this->display_header();
                echo implode(PHP_EOL, $html);
                $this->display_footer();
            }
        }
    }

    function get_allowed_content_object_types()
    {
        return array(Survey :: CLASS_NAME);
    }
}
?>