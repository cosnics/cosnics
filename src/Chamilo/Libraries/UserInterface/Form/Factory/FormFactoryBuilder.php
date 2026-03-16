<?php
namespace Chamilo\Libraries\UserInterface\Form\Factory;

use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\DependencyInjection\Service\DependencyInjectionContainerBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\DependencyInjection\DependencyInjectionExtension;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;

/**
 * @package Chamilo\Libraries\UserInterface\Form\Factory
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FormFactoryBuilder
{
    protected ArrayCollection $additionalFormTypes;

    protected ChamiloRequest $request;

    public function __construct(ChamiloRequest $request)
    {
        $this->additionalFormTypes = new ArrayCollection();
        $this->request = $request;
    }

    public function addAdditionalFormType(FormTypeInterface $formType): FormFactoryBuilder
    {
        $this->additionalFormTypes->set(get_class($formType), $formType);

        return $this;
    }

    public function createFormFactory(): FormFactoryInterface
    {
        $requestStack = new RequestStack([$this->getRequest()]);

        $csrfGenerator = new UriSafeTokenGenerator();
        $csrfStorage = new SessionTokenStorage($requestStack);
        $csrfManager = new CsrfTokenManager($csrfGenerator, $csrfStorage);

        $formFactoryBuilder = Forms::createFormFactoryBuilder();

        $formFactoryBuilder->addExtension(new HttpFoundationExtension());
        $formFactoryBuilder->addExtension(new CsrfExtension($csrfManager));
        $formFactoryBuilder->addExtension(
            new DependencyInjectionExtension(
                DependencyInjectionContainerBuilder::getInstance()->createContainer(), [], []
            )
        );
        $formFactoryBuilder->addTypes($this->getAdditionalFormTypes()->toArray());

        return $formFactoryBuilder->getFormFactory();
    }

    public function getAdditionalFormTypes(): ArrayCollection
    {
        return $this->additionalFormTypes;
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }
}