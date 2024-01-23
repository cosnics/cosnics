<?php

namespace Chamilo\Application\Plagiarism\API\StrikePlagiarism\Model;

class WebhookRequestData
{
    protected string $id;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): WebhookRequestData
    {
        $this->id = $id;
        return $this;
    }
}