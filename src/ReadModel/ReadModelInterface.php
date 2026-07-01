<?php

declare(strict_types=1);

namespace Shared\ReadModel;

/**
 * A read model: a projection of events shaped for querying and presentation. A
 * marker, since read models differ in how they are identified — most carry a
 * Uuid (and declare id() themselves), while join projections are keyed by their
 * natural columns. The contract intentionally imposes no identity.
 *
 * Lookup/search indices are not read models: they are never presented, only
 * resolved by key. Those implement Shared\Index\IndexInterface instead.
 */
interface ReadModelInterface
{
}
