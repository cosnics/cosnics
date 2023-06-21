<?php
namespace Chamilo\Core\Admin\Announcement\Service\Publication;

use Chamilo\Core\Admin\Announcement\Manager;
use Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository\Publication\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationAttributesGenerator
{

    protected UrlGenerator $urlGenerator;

    private Translator $translator;

    public function __construct(Translator $translator, UrlGenerator $urlGenerator)
    {
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
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
        $attributes->set_application(Manager::CONTEXT);

        $attributes->set_location(
            $this->getTranslator()->trans('TypeName', [], Manager::CONTEXT)
        );

        $viewUrl = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_VIEW,
                Manager::PARAM_SYSTEM_ANNOUNCEMENT_ID => $record[Publication::PROPERTY_ID]
            ]
        );

        $attributes->set_url($viewUrl);
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

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }
}