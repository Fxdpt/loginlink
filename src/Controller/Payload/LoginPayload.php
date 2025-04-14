<?php

namespace App\Controller\Payload;

use Symfony\Component\Validator\Constraints as Assert;

final class LoginPayload
{
    #[Assert\NotNull]
    #[Assert\Email]
    public $email;
}