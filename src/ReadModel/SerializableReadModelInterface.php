<?php

declare(strict_types=1);

namespace Shared\ReadModel;

use Shared\Domain\IdentifiableInterface;
use Shared\Serializer\SerializableInterface;

interface SerializableReadModelInterface extends SerializableInterface, IdentifiableInterface
{
}
