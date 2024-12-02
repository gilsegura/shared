<?php

declare(strict_types=1);

namespace Shared\ReadModel;

use Serializer\SerializableInterface;
use Shared\Domain\IdentifiableInterface;

interface SerializableReadModelInterface extends SerializableInterface, IdentifiableInterface
{
}
