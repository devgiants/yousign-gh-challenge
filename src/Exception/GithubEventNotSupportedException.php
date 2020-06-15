<?php

declare(strict_types=1);


namespace App\Exception;


use Symfony\Component\Console\Exception\InvalidArgumentException;

class GithubEventNotSupportedException extends InvalidArgumentException
{

}
