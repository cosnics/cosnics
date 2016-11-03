<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Form;

use Chamilo\Core\Repository\ContentObject\RssFeed\Storage\DataClass\RssFeed;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: rss_feed_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.content_object.rss_feed
 */
class RssFeedForm extends ContentObjectForm
{
    const DEFAULT_FEED_URL = 'http://';
    const DEFAULT_NUMBER_OF_ENTRIES = 20;
    const MAXIMUM_NUMBER_OF_ENTRIES = 50;

    protected function build_creation_form()
    {
        parent:: build_creation_form();
        $this->buildElements();
    }

    protected function build_editing_form()
    {
        parent:: build_editing_form();
        $this->buildElements();
    }

    public function setDefaults($defaults = array())
    {
        $lo = $this->get_content_object();
        $default_url = null;
        $default_number_entries = null;
        if (isset($lo))
        {
            $default_url = $lo->get_url();
            $default_number_entries = $lo->get_number_of_entries();
        }
        $defaults[RssFeed::PROPERTY_URL] = is_null($default_url) ? self::DEFAULT_FEED_URL : $default_url;

        $defaults[RssFeed::PROPERTY_NUMBER_OF_ENTRIES] =
            is_null($default_number_entries) ? self::DEFAULT_NUMBER_OF_ENTRIES : $default_number_entries;

        parent:: setDefaults($defaults);
    }

    public function create_content_object()
    {
        $content_object = new RssFeed();
        $content_object->set_url($this->exportValue(RssFeed :: PROPERTY_URL));
        $content_object->set_number_of_entries($this->exportValue(RssFeed :: PROPERTY_NUMBER_OF_ENTRIES));

        $this->set_content_object($content_object);

        return parent:: create_content_object();
    }

    public function update_content_object()
    {
        $content_object = $this->get_content_object();
        $content_object->set_url($this->exportValue(RssFeed :: PROPERTY_URL));
        $content_object->set_number_of_entries($this->exportValue(RssFeed :: PROPERTY_NUMBER_OF_ENTRIES));

        return parent:: update_content_object();
    }

    protected function buildElements()
    {
        $this->addElement('category', Translation:: get('Properties'));

        $this->add_textfield(
            RssFeed :: PROPERTY_URL,
            Translation:: get('URL'),
            true,
            ' size="100" style="width: 100%;"'
        );

        $this->add_textfield(
            RssFeed::PROPERTY_NUMBER_OF_ENTRIES,
            Translation::getInstance()->getTranslation(
                'NumberOfEntries', array(), 'Chamilo\Core\Repository\ContentObject\RssFeed'
            ), true, ' size="100" style="width: 100%;"'
        );

        $this->addElement('category');
    }

    function check_number_entries($value)
    {
        if ($value < 1 || $value > self::MAXIMUM_NUMBER_OF_ENTRIES)
        {
            return false;
        }

        return true;
    }
}
