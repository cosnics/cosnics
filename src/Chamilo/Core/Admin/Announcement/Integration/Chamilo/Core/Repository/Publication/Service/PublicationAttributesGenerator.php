<?php
namespace Chamilo\Core\Admin\Announcement\Integration\Chamilo\Core\Repository\Publication\Service;

use Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationAttributesGenerator
{

    /**
     *
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     *
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param string[] $record
     *
     * @return \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes
     */
    public function createAttributesFromRecord($record)
    {
        $attributes = new Attributes();

        $attributes->setId($record[Publication::PROPERTY_ID]);
        $attributes->set_publisher_id($record[Publication::PROPERTY_PUBLISHER_ID]);
        $attributes->set_date($record[Publication::PROPERTY_PUBLICATION_DATE]);
        $attributes->set_application(\Chamilo\Core\Admin\Announcement\Manager::context());

        $attributes->set_location(
            $this->getTranslator()->trans('TypeName', [], \Chamilo\Core\Admin\Announcement\Manager::context())
        );

        $redirect = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Announcement\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Core\Admin\Announcement\Manager::ACTION_VIEW,
                \Chamilo\Core\Admin\Announcement\Manager::PARAM_SYSTEM_ANNOUNCEMENT_ID => $record[Publication::PROPERTY_ID]
            )
        );

        $attributes->set_url($redirect->getUrl());
        $attributes->set_title($record[ContentObject::PROPERTY_TITLE]);
        $attributes->set_content_object_id($record[Publication::PROPERTY_CONTENT_OBJECT_ID]);
        $attributes->setModifierServiceIdentifier(PublicationModifier::class);

        return $attributes;
    }

    /**
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }
}