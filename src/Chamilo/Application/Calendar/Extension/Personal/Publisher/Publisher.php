<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Publisher;

use Chamilo\Application\Calendar\Extension\Personal\Form\PublicationForm;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application\calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Publisher
{

    /**
     *
     * @var Application
     */
    private $application;

    /**
     *
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     *
     * @param int[] $ids
     */
    public function get_publications_form($ids)
    {
        if (is_null($ids))
        {
            return '';
        }

        if (! is_array($ids))
        {
            $ids = array($ids);
        }

        $parameters = $this->application->get_parameters();
        $parameters[\Chamilo\Core\Repository\Viewer\Manager :: PARAM_ID] = $ids;
        $parameters[\Chamilo\Core\Repository\Viewer\Manager :: PARAM_ACTION] = \Chamilo\Core\Repository\Viewer\Manager :: ACTION_PUBLISHER;

        $form = new PublicationForm(
            PublicationForm :: TYPE_MULTI,
            $ids,
            $this->application->get_user(),
            $this->application->get_url($parameters));

        if ($form->validate())
        {
            $publication = $form->create_content_object_publications();

            if (! $publication)
            {
                $message = Translation :: get(
                    'ObjectNotPublished',
                    array('OBJECT' => Translation :: get('PersonalCalendar')),
                    Utilities :: COMMON_LIBRARIES);
            }
            else
            {
                $message = Translation :: get(
                    'ObjectPublished',
                    array('OBJECT' => Translation :: get('PersonalCalendar')),
                    Utilities :: COMMON_LIBRARIES);
            }

            $this->application->redirect(
                $message,
                (! $publication ? true : false),
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Application\Calendar\Manager :: context(),
                    Application :: PARAM_ACTION => \Chamilo\Application\Calendar\Manager :: ACTION_BROWSE));
        }
        else
        {
            $html = array();

            $html[] = $this->application->render_header();

            if (count($ids) > 0)
            {
                $parameters = new DataClassRetrievesParameters(
                    new InCondition(
                        new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID),
                        $ids,
                        ContentObject :: get_table_name()));

                $content_objects = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_active_content_objects(
                    ContentObject :: class_name(),
                    $parameters);

                $html[] = '<div class="content_object padding_10">';
                $html[] = '<div class="title">' . Translation :: get(
                    'SelectedContentObjects',
                    null,
                    Utilities :: COMMON_LIBRARIES) . '</div>';
                $html[] = '<div class="description">';
                $html[] = '<ul class="attachments_list">';

                while ($content_object = $content_objects->next_result())
                {
                    $namespace = ClassnameUtilities :: getInstance()->getNamespaceFromClassname(
                        $content_object->get_type());
                    $html[] = '<li><img src="' . Theme :: getInstance()->getImagePath($namespace) . 'Logo/' .
                         Theme :: ICON_MINI . '.png" alt="' .
                         htmlentities(Translation :: get('TypeName', null, $namespace)) . '"/> ' .
                         $content_object->get_title() . '</li>';
                }

                $html[] = '</ul>';
                $html[] = '</div>';
                $html[] = '</div>';
            }

            $html[] = $form->toHtml();
            $html[] = '<div style="clear: both;"></div>';
            $html[] = $this->application->render_footer();

            return implode("\n", $html);
        }
    }
}
