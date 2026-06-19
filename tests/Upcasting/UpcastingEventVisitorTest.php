<?php

declare(strict_types=1);

namespace Shared\Tests\Upcasting;

use PHPUnit\Framework\TestCase;
use Shared\Domain\DomainMessage;
use Shared\Domain\Metadata;
use Shared\Domain\Uuid;
use Shared\EventStore\CallableEventVisitor;
use Shared\Upcasting\SequentialUpcasterChain;
use Shared\Upcasting\UpcastingEventVisitor;

final class UpcastingEventVisitorTest extends TestCase
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

    public function test_must_upcast_the_message_before_delegating_to_the_inner_visitor(): void
    {
        $seen = [];

        $visitor = new UpcastingEventVisitor(
            new CallableEventVisitor(
                static function (DomainMessage $message) use (&$seen): void {
                    $seen[] = $message;
                }
            ),
            new SequentialUpcasterChain(new EventWasOccurredV1ToV2Upcaster()),
        );

        $visitor($this->message());

        self::assertCount(1, $seen);
        self::assertInstanceOf(EventV2WasOccurred::class, $seen[0]->payload);
    }

    public function test_must_pass_the_message_through_when_the_chain_is_empty(): void
    {
        $seen = [];

        $visitor = new UpcastingEventVisitor(
            new CallableEventVisitor(
                static function (DomainMessage $message) use (&$seen): void {
                    $seen[] = $message;
                }
            ),
            new SequentialUpcasterChain(),
        );

        $message = $this->message();
        $visitor($message);

        self::assertSame([$message], $seen);
    }
}
