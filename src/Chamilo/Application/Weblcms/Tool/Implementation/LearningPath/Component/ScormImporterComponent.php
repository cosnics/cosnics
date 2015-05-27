<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Component;

use Chamilo\Application\Weblcms\ContentObjectPublisher;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager;
use Chamilo\Core\Repository\Common\Import\ContentObjectImportController;
use Chamilo\Core\Repository\Common\Import\ImportParameters;
use Chamilo\Core\Repository\Form\ContentObjectImportForm;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Properties\FileProperties;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: learning_path_scorm_importer.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.learning_path.component
 */
class ScormImporterComponent extends Manager
{

    public function run()
    {
        $parameters = array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_IMPORT_SCORM);
        $import_form = ContentObjectImportForm :: factory('scorm', $this, 'post', $this->get_url($parameters));

        $objects = Request :: get('objects');

        if ($import_form->validate() || $objects)
        {
            if (! $objects)
            {
                $values = $import_form->exportValues();
                $parent_id = $values[ContentObject :: PROPERTY_PARENT_ID];
                $new_category_name = $values[ContentObjectImportForm :: NEW_CATEGORY];

                if (! StringUtilities :: getInstance()->isNullOrEmpty($new_category_name, true))
                {
                    $new_category = new RepositoryCategory();
                    $new_category->set_name($new_category_name);
                    $new_category->set_parent($parent_id);
                    $new_category->set_user_id($this->get_user_id());
                    $new_category->set_type(RepositoryCategory :: TYPE_NORMAL);
                    if (! $new_category->create())
                    {
                        throw new \Exception(Translation :: get('CategoryCreationFailed'));
                    }
                    else
                    {
                        $category_id = $new_category->get_id();
                    }
                }
                else
                {
                    $category_id = $parent_id;
                }

                if (isset($_FILES[ContentObjectImportForm :: IMPORT_FILE_NAME]))
                {
                    $file = FileProperties :: from_upload($_FILES[ContentObjectImportForm :: IMPORT_FILE_NAME]);
                }
                else
                {
                    $file = null;
                }

                $parameters = ImportParameters :: factory(
                    $import_form->exportValue(ContentObjectImportForm :: PROPERTY_TYPE),
                    $this->get_user_id(),
                    $category_id,
                    $file,
                    $values);
                $controller = ContentObjectImportController :: factory($parameters);
                $lo_ids = $controller->run();

                $messages = $controller->get_messages_for_url();

                Session :: register(Application :: PARAM_MESSAGES, $messages);
            }
            else
            {
                $lo_ids = $objects;
            }

            $this->set_parameter('objects', $lo_ids);
            $publisher = new ContentObjectPublisher($this, $lo_ids);

            if ($publisher->ready_to_publish())
            {
                $success = $publisher->publish();

                $message = Translation :: get(
                    ($success ? 'ObjectPublished' : 'ObjectNotPublished'),
                    array('OBJECT' => Translation :: get('Object')),
                    Utilities :: COMMON_LIBRARIES);

                $parameters = array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_BROWSE);

                if ($publisher->is_publish_and_build_submit())
                {
                    $parameters[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION] = \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_BUILD_COMPLEX_CONTENT_OBJECT;

                    $publications = $publisher->get_publications();
                    $parameters[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID] = $publications[0]->get_id();
                }

                $this->redirect($message, ! $success, $parameters);
            }
            else
            {
                $html = array();

                $html[] = $this->render_header();
                $html[] = $publisher->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $import_form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function with_mail_option()
    {
        return false;
    }
}
