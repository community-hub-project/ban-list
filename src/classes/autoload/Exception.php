<?php
declare(strict_types=1);

namespace CommunityHub\Components\BanList;

use Throwable;

/**
 * Exception class for all exceptions thrown from within this package.
 */
final class Exception extends \Exception
{
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
