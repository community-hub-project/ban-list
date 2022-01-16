# Ban List

A lightweight sqlite-powered package for banning IP Addresses
from visiting a web application.

## Usage

An instance of `\CommunityHub\Component\BanList\EndPoint` is required to add,
remove, or check and enpoint. This class translates human-readable names for
HTTP end points into more detailed string descriptions which are persisted to
a file.

That file is managed by instances of `\CommunityHub\Component\BanList\Store`.
The store holds all banned end points and can be used to add banned endpoints,
remove banned end points, or check whether specific end points have been banned.
It can also be cleared of all banned end points if required.

### Checking if an endpoint is banned.

The simplest way to check if and endpoint is banned is like this:

    <?php

    $endPoint = \CommunityHub\Component\BanList\EndPoint::forCurrentRequest($_SERVER);
    $store = new \CommunityHub\Component\BanList\Store($endPoint);

    if ($store->check($endPoint)) {
        exit('You are banned!');
    }

### Endpoints

The endpoint class has 2 public methods.

The first is `forCurrentRequest` which takes the `$_SERVER` super global as a
parameter. It creates an instance of itself for the current HTTP request.
It throws instances of `\CommunityHub\Component\BanList\Exception` if `$_SERVER`
does not contain the required data.

The second if `make` which converts a human-readable representation of an
endpoint into an endpoint object. Currently, the human-readable representation
is simply the IP address. That may change in future versions if more complicated
endpoints descriptions are required. It throws instances of
`\CommunityHub\Component\BanList\Exception` if the human-readable representation
is not a valid IP address.

### The Store

The store class has 4 public methods.

The first is `add` Which adds an end point to the store. The second is `remove`
which removes an end point from the store. The third is `check` which returns
`true` if an end point is banned and `false` if it is not. The fourth is `clear`
which removes all end points from the store. All except `clear` take one end
point as their only parameter. All throw an instance of
`\CommunityHub\Component\BanList\Exception` if there was an error performing
their respective tasks.

The store takes 1 argument in it's constructor: the path to the file to store
the end points in. It throws an instance of
`\CommunityHub\Component\BanList\Exception` if the file could not be created
or properly initialized.

### Helper functions

This package contains 2 helper functions.

The first is `\CommunityHub\Component\BanList\makeStore` which returns an
instance of `\CommunityHub\Component\BanList\Store`. The helper method takes 1
optional argument: the file path in which to store the endpoints. If the path is
not provided then it defaults to a file named `ban_list.sqlite` in the project
root directory. It throws an instance of
`\CommunityHub\Component\BanList\Exception` if the file could not be created
or properly initialized.

The second is `\CommunityHub\Component\BanList\isBanned` which returns `true` if
the current HTTP endpoint is banned and `false` if it is not. The helper method
takes 1 optional argument: the file path in which to store the endpoints. If the
path is not provided then it defaults to a file named `ban_list.sqlite` in the
project root directory. It throws an instance of
`\CommunityHub\Component\BanList\Exception` if the file could not be created
or properly initialized.

### Running the ban list before autoload

For the sake of performance, it may be desirable to run the ban list on every
incoming HTTP request before initializing the composer autoloader.

The file `bootstrap.php` loads all the classes needed to use this package.
So to check an end point without auto-loading require that file, check the
endpoint, and then require `composer/autoload.php`.

    <?php

    require `vendor/community-hub/ban-list/bootstrap.php`

    $endPoint = \CommunityHub\Component\BanList\EndPoint::forCurrentRequest($_SERVER);
    $store = new \CommunityHub\Component\BanList\Store($endPoint);

    if ($store->check($endPoint)) {
        exit('You are banned!');
    }

    require `vendor/autoload.php`

Alternatively, the helper functions also require the bootstrap file.

    <?php

    require `vendor/community-hub/ban-list/src/helpers.php`

    $isBanned = \CommunityHub\Component\BanList\EndPoint::isBanned();

    if ($store->check($isBanned)) {
        exit('You are banned!');
    }

    require `vendor/autoload.php`
