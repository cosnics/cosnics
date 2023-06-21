<?php
namespace Chamilo\Application\Weblcms\Service\Publication;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Publication\Manager;
use Chamilo\Libraries\Format\Form\FormValidator;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationTargetRenderer extends \Chamilo\Core\Repository\Publication\Service\PublicationTargetRenderer
{

    /**
     * @throws \QuickformException
     */
    public function addPublicationAttributes(FormValidator $form, string $publicationContext)
    {
        $applicationContext = \Chamilo\Application\Weblcms\Manager::CONTEXT;
        $labelPublicationDetails = $this->getTranslator()->trans('PublicationDetails', [], $applicationContext);
        $labelHidden = $this->getTranslator()->trans('Hidden', [], $applicationContext);
        $labelCollaborate = $this->getTranslator()->trans('CourseAdminCollaborate', [], $applicationContext);
        $labelEmail = $this->getTranslator()->trans('SendByEMail', [], $applicationContext);

        $form->addElement('html', '<h5>' . $labelPublicationDetails . '</h5>');

        $form->addElement(
            'checkbox',
            $this->getPublicationAttributeElementName($publicationContext, ContentObjectPublication::PROPERTY_HIDDEN),
            $labelHidden
        );

        $form->addTimePeriodSelection(
            'PublicationPeriod', ContentObjectPublication::PROPERTY_FROM_DATE,
            ContentObjectPublication::PROPERTY_TO_DATE, FormValidator::PROPERTY_TIME_PERIOD_FOREVER,
            $this->getPublicationAttributeElementName($publicationContext)
        );

        $form->addElement(
            'checkbox', $this->getPublicationAttributeElementName(
            $publicationContext, ContentObjectPublication::PROPERTY_ALLOW_COLLABORATION
        ), $labelCollaborate
        );

        $form->addElement(
            'checkbox', $this->getPublicationAttributeElementName(
            $publicationContext, ContentObjectPublication::PROPERTY_EMAIL_SENT
        ), $labelEmail
        );

        $defaults[Manager::WIZARD_TARGET][$publicationContext][Manager::WIZARD_OPTION][FormValidator::PROPERTY_TIME_PERIOD_FOREVER] =
            1;
        $defaults[Manager::WIZARD_TARGET][$publicationContext][Manager::WIZARD_OPTION][ContentObjectPublication::PROPERTY_ALLOW_COLLABORATION] =
            1;

        $form->setDefaults($defaults);
    }

    /**
     * @param string $publicationContext
     * @param ?string $property
     *
     * @return string
     */
    protected function getPublicationAttributeElementName(string $publicationContext, string $property = null): string
    {
        $elementName = Manager::WIZARD_TARGET . '[' . $publicationContext . '][' . Manager::WIZARD_OPTION . ']';

        if (!is_null($property))
        {
            $elementName .= '[' . $property . ']';
        }

        return $elementName;
    }
}