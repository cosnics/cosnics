<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Service\Publication;

use Chamilo\Application\Calendar\Extension\Personal\Manager;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Publication as RepositoryPublication;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Calendar\Extension\Personal\Service\Publication
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

        $attributes->setId($record[DataClass::PROPERTY_ID]);
        $attributes->set_publisher_id($record[Publication::PROPERTY_PUBLISHER]);
        $attributes->set_date($record[Publication::PROPERTY_PUBLISHED]);
        $attributes->set_application(Manager::CONTEXT);

        $attributes->set_location(
            $this->getTranslator()->trans('TypeName', [], \Chamilo\Application\Calendar\Manager::CONTEXT)
        );

        $url = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_VIEW,
                Manager::PARAM_PUBLICATION_ID => $record[DataClass::PROPERTY_ID]
            ]
        );

        $attributes->set_url($url);
        $attributes->set_title($record[ContentObject::PROPERTY_TITLE]);
        $attributes->set_content_object_id($record[RepositoryPublication::PROPERTY_CONTENT_OBJECT_ID]);
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