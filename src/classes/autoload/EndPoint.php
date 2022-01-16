<?php
declare(strict_types=1);

namespace CommunityHub\Components\BanList;

use function filter_var;
use function sprintf;

use const FILTER_VALIDATE_IP;

/**
 * HTTP Endpoint
 *
 * Representation of a HTTP endpoint. Used to convert all the data surrounding a
 * HTTP EndPoint into a string key which can be checked against or persisted to
 * a ban list store.
 *
 * Should the ban list ever require more than just the $_SERVER['REMOTE_ADDR']
 * the changes should be reflected in this class.
 */
final class EndPoint
{
    private string $ipAddress;

    /**
     * @param array $server a copy of the $_SERVER super-global.
     * @throws Exception
     *     If $_SERVER['REMOTE_ADDR'] is not defined or is not a valid IP address.
     */
    public static function forCurrentRequest(array $server): self
    {
        if (!isset($server['REMOTE_ADDR'])) {
            throw self::createException('Missing required $_SERVER key: REMOTE_ADDR.');
        }

        return self::make($server['REMOTE_ADDR']);
    }

    /**
     * Make an endpoint from a human-readable string format.
     *
     * @throws Exception
     *     If the string format is not a valid IPV4 or IPV6 address.
     */
    public static function make(string $endpoint): self
    {
        return new self($endpoint);
    }

    /**
     * @throws Exception
     *     If the string format is not a valid IPV4 or IPV6 address.
     */
    private function __construct(string $ipAddress)
    {
        if (!filter_var($ipAddress, FILTER_VALIDATE_IP)) {
            $message = sprintf('Invalid IP address: %s.', $ipAddress);

            throw self::createException($message);
        }

        $this->ipAddress = $ipAddress;
    }

    /**
     * @return string a string representation of the HTTP endpoint. This format
     *     does not need to be human readable. It represents all the data
     *     required to identify a HTTP endpoint to the most
     *     accurate details possible.
     */
    public function __toString(): string
    {
        return $this->ipAddress;
    }

    private static function createException(string $message): Exception
    {
        require_once __DIR__ . '/Exception.php';

        return new Exception($message);
    }
}
