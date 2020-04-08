<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Service;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Publication\Manager;
use Chamilo\Libraries\Format\Form\FormValidator;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationTargetRenderer extends \Chamilo\Core\Repository\Publication\Service\PublicationTargetRenderer
{
    /**
     * @param \Chamilo\Libraries\Format\Form\FormValidator $form
     * @param string $publicationContext
     *
     * @throws \Exception
     */
    public function addPublicationAttributes(FormValidator $form, string $publicationContext)
    {

        $applicationContext = \Chamilo\Application\Weblcms\Manager::context();
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

        $form->add_forever_or_timewindow(
            'PublicationPeriod', $this->getPublicationAttributeElementName($publicationContext), true
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

        $defaults[Manager::WIZARD_TARGET][$publicationContext][Manager::WIZARD_OPTION]['forever'] = 1;
        $defaults[Manager::WIZARD_TARGET][$publicationContext][Manager::WIZARD_OPTION][ContentObjectPublication::PROPERTY_ALLOW_COLLABORATION] =
            1;

        $form->setDefaults($defaults);
    }

    /**
     * @param string $publicationContext
     * @param string
     *
     * @return string
     */
    protected function getPublicationAttributeElementName(string $publicationContext, string $property = null)
    {
        $elementName = Manager::WIZARD_TARGET . '[' . $publicationContext . '][' . Manager::WIZARD_OPTION . ']';

        if (!is_null($property))
        {
            $elementName .= '[' . $property . ']';
        }

        return $elementName;
    }
}