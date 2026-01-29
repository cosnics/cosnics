<?php
namespace Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface;

/**
 * @package Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ButtonActionInterface extends ButtonInterface
{
    public function getAction(): ?string;

    public function getConfirmationMessage(): ?string;

    public function getTarget(): ?string;

    public function setAction(?string $action): static;

    public function setConfirmationMessage(?string $message): static;

    public function setTarget(?string $target): static;
}
