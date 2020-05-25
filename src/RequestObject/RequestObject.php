<?php

declare(strict_types=1);

namespace App\RequestObject;

/**
 * Marker interface to use it in the Argument resolver.
 * Allows to do mapping and deserialization from request to object and validate it.
 *
 * @see App\Resolver\RequestObjectResolver
 */
interface RequestObject
{
}
