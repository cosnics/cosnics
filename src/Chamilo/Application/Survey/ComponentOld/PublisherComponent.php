<?php
namespace Chamilo\Application\Survey\Component;

use Chamilo\Application\Survey\Form\PublicationForm;
use Chamilo\Application\Survey\Manager;
use Chamilo\Application\Survey\Rights\Rights;
use Chamilo\Core\Repository\ContentObject\Survey\Storage\DataClass\Survey;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

class PublisherComponent extends Manager implements \Chamilo\Core\Repository\Viewer\ViewerInterface
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        if (! Rights :: getInstance()->publication_is_allowed())
        {
            throw new NotAllowedException();
        }

        $html[] = $this->render_header();

        if (! \Chamilo\Core\Repository\Viewer\Manager :: is_ready_to_be_published())
        {
            $factory = new ApplicationFactory(
                \Chamilo\Core\Repository\Viewer\Manager :: context(),
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
            return $factory->run();
        }
        else
        {

            $html = array();
            $html[] = $this->render_header();

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
                    $html[] = '<li><img src="' . Theme :: getInstance()->getImagePath('Chamilo\Application\Survey') .
                         'survey-22.png" alt="' .
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
                $html[] = $this->render_footer();
                return implode(PHP_EOL, $html);
            }
        }
    }

    function get_allowed_content_object_types()
    {
        return array(Survey :: class_name());
    }
}
?>