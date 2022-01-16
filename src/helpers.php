<?php
declare(strict_types=1);

namespace CommunityHub\Components\BanList;

/**
 * @throws Exception
 *     If the sqlite file path was ":memory" (in memory databases are
 *     strictly forbidden here). Or if the file/directory of the file path
 *     did not exist and could not be created. Or if the sqlite database
 *     connection could not be established.
 */
function makeStore(?string $filePath = null): Store
{
    require_once __DIR__ . '/../bootstrap.php';

    $filePath = $filePath ?? __DIR__ . '/../../../ban_list.sqlite';

    return new Store($filePath);
}

/**
 * @throws Exception
 *     If the sqlite file path was ":memory" (in memory databases are
 *     strictly forbidden here). Or if the file/directory of the file path
 *     did not exist and could not be created. Or if the sqlite database
 *     connection could not be established. Or is $_SERVER['REMOTE_ADDR']
 *     is not defined.
 */
function isBanned(?string $filePath = null): bool
{
    require_once __DIR__ . '/../bootstrap.php';

    $endPoint = \CommunityHub\Components\BanList\EndPoint::forCurrentRequest($_SERVER);

    return makeStore($filePath)->check($endPoint);
}
