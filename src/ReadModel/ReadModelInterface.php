<?php

declare(strict_types=1);

namespace Shared\ReadModel;

/**
 * A read model: a projection of events shaped for querying. A marker, since
 * projections differ in how they are identified — resource projections carry a
 * Uuid (and declare id() themselves), while index or join projections are keyed
 * by their natural columns. The contract intentionally imposes no identity.
 */
interface ReadModelInterface
{
}
