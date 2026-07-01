<?php

declare(strict_types=1);

namespace Shared\Index;

/**
 * A typed lookup key for an index. An index answers one question — "which ids
 * are filed under this key?" — so the key names exactly the axis or axes it is
 * built on, and nothing more. It is not a criterion: an index resolves only its
 * own complete key, which is what keeps it an index and not a queryable model.
 * A marker, since each index defines the shape of its own key.
 */
interface IndexKeyInterface
{
}
