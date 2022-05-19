<?php
namespace Chamilo\Core\Repository\ContentObject\Vimeo\Form;

use Chamilo\Core\Repository\ContentObject\Vimeo\Storage\DataClass\Vimeo;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Instance\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @author Shoira Mukhsinova
 */
class VimeoForm extends ContentObjectForm
{

    protected function build_creation_form($htmleditor_options = [], $in_tab = false)
    {
        parent::build_creation_form();
        $this->addElement('category', Translation::get('Properties'));

        $external_repositories = Manager::get_links(Vimeo::getTypeName(), true);

        if ($external_repositories)
        {
            $this->addElement('static', null, null, $external_repositories);
        }

        $this->addElement('hidden', SynchronizationData::PROPERTY_EXTERNAL_ID);
        $this->addElement('hidden', SynchronizationData::PROPERTY_EXTERNAL_OBJECT_ID);
    }

    protected function build_editing_form($htmleditor_options = [], $in_tab = false)
    {
        parent::build_editing_form();
        $this->addElement('category', Translation::get('Properties'));
    }

    public function create_content_object()
    {
        $object = new Vimeo();
        $this->set_content_object($object);

        $success = parent::create_content_object();

        if ($success)
        {
            $external_repository_id = (int) $this->exportValue(SynchronizationData::PROPERTY_EXTERNAL_ID);

            $external_respository_sync = new SynchronizationData();
            $external_respository_sync->set_external_id($external_repository_id);
            $external_respository_sync->set_external_object_id(
                (string) $this->exportValue(SynchronizationData::PROPERTY_EXTERNAL_OBJECT_ID)
            );
            $external_object = $external_respository_sync->get_external_object();

            SynchronizationData::quicksave($object, $external_object, $external_repository_id);
        }

        return $success;
    }
}
