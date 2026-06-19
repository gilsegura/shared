<?php

declare(strict_types=1);

namespace Shared\Tests\Upcasting;

use PHPUnit\Framework\TestCase;
use Shared\Domain\DomainMessage;
use Shared\Domain\Metadata;
use Shared\Domain\Uuid;
use Shared\Upcasting\SequentialUpcasterChain;

final class SequentialUpcasterChainTest extends TestCase
{
    private function message(): DomainMessage
    {
        return DomainMessage::record(
            new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'),
            0,
            Metadata::empty(),
            new EventV1WasOccurred()
        );
    }

    public function test_must_pass_the_message_through_unchanged_when_there_are_no_upcasters(): void
    {
        $chain = new SequentialUpcasterChain();

        $message = $this->message();
        $result = iterator_to_array(($chain)($message));

        self::assertCount(1, $result);
        self::assertSame($message, $result[0]);
    }

    public function test_must_apply_a_single_upcaster(): void
    {
        $chain = new SequentialUpcasterChain(new EventWasOccurredV1ToV2Upcaster());

        $result = iterator_to_array(($chain)($this->message()));

        self::assertCount(1, $result);
        self::assertInstanceOf(EventV2WasOccurred::class, $result[0]->payload);
    }

    public function test_must_feed_each_upcaster_the_output_of_the_previous_one(): void
    {
        // The first upcaster turns V1 into V2; the second only acts on V2, so it
        // can only succeed if it receives the first one's output, not the
        // original message. The chain yields a single, fully upcast message.
        $chain = new SequentialUpcasterChain(
            new EventWasOccurredV1ToV2Upcaster(),
            new EventWasOccurredV2ToV3Upcaster(),
        );

        $result = iterator_to_array(($chain)($this->message()));

        self::assertCount(1, $result);
        self::assertInstanceOf(EventV3WasOccurred::class, $result[0]->payload);
    }
}
