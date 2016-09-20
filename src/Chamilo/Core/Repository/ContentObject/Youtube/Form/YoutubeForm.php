<?php
namespace Chamilo\Core\Repository\ContentObject\Youtube\Form;

use Chamilo\Core\Repository\ContentObject\Youtube\Storage\DataClass\Youtube;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: youtube_form.class.php
 *
 * @package repository.lib.content_object.youtube
 */
class YoutubeForm extends ContentObjectForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get('Properties'));

        $external_repositories = \Chamilo\Core\Repository\Instance\Manager :: get_links(
            array(Youtube :: package()),
            true);

        if ($external_repositories)
        {
            $this->addElement('static', null, null, $external_repositories);
        }

        $this->addElement('hidden', SynchronizationData :: PROPERTY_EXTERNAL_ID);
        $this->addElement('hidden', SynchronizationData :: PROPERTY_EXTERNAL_OBJECT_ID);

        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent :: build_creation_form();
    }

    public function create_content_object()
    {
        $object = new Youtube();
        $this->set_content_object($object);

        $success = parent :: create_content_object();

        if ($success)
        {
            $external_repository_id = (int) $this->exportValue(SynchronizationData :: PROPERTY_EXTERNAL_ID);

            $external_respository_sync = new SynchronizationData();
            $external_respository_sync->set_external_id($external_repository_id);

            $external_repository_object_id = (string) $this->exportValue(
                SynchronizationData :: PROPERTY_EXTERNAL_OBJECT_ID);

            if (! $external_repository_object_id)
            {
                return false;
            }

            $external_respository_sync->set_external_object_id($external_repository_object_id);

            $external_object = $external_respository_sync->get_external_object();

            SynchronizationData :: quicksave($object, $external_object, $external_repository_id);
        }

        return $success;
    }

    /*
     * function update_content_object() { $object = $this->get_content_object(); return parent ::
     * update_content_object(); }
     */
}
