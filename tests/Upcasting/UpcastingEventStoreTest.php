<?php

declare(strict_types=1);

namespace Shared\Tests\Upcasting;

use PHPUnit\Framework\TestCase;
use Shared\Criteria;
use Shared\Domain\DomainEventStream;
use Shared\Domain\DomainMessage;
use Shared\Domain\Metadata;
use Shared\Domain\Uuid;
use Shared\EventStore\CallableEventVisitor;
use Shared\Tests\EventStore\InMemoryEventStore;
use Shared\Upcasting\SequentialUpcasterChain;
use Shared\Upcasting\UpcastingEventStore;

final class UpcastingEventStoreTest extends TestCase
{
    public function test_must_upcast_event_when_stream_is_not_empty(): void
    {
        $store = new UpcastingEventStore(
            new InMemoryEventStore(),
            new SequentialUpcasterChain(
                new EventWasOccurredV1ToV2Upcaster()
            )
        );

        $store->append(new DomainEventStream(DomainMessage::record(
            new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'),
            0,
            Metadata::empty(),
            new EventV1WasOccurred()
        )));

        $stream = $store->load(new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'));

        $messages = $stream->messages;
        $message = $messages[0];
        $event = $message->payload;

        self::assertInstanceOf(DomainMessage::class, $message);
        self::assertInstanceOf(EventV2WasOccurred::class, $event);
    }

    public function test_must_manage_event_when_stream_is_not_empty(): void
    {
        $store = new UpcastingEventStore(
            new InMemoryEventStore(),
            new SequentialUpcasterChain()
        );

        $store->append(new DomainEventStream(DomainMessage::record(
            new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'),
            0,
            Metadata::empty(),
            new EventV1WasOccurred()
        )));

        $messages = [];

        $store->visitEvents(new Criteria\AndX(new Criteria\EqId(new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'))), new CallableEventVisitor(
            static function (DomainMessage $message) use (&$messages): void {
                $messages[] = $message;
            }
        ));

        $message = $messages[0];
        $event = $message->payload;

        self::assertInstanceOf(DomainMessage::class, $message);
        self::assertInstanceOf(EventV1WasOccurred::class, $event);
    }

    public function test_must_upcast_events_when_visiting_them(): void
    {
        $store = new UpcastingEventStore(
            new InMemoryEventStore(),
            new SequentialUpcasterChain(
                new EventWasOccurredV1ToV2Upcaster()
            )
        );

        $store->append(new DomainEventStream(DomainMessage::record(
            new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'),
            0,
            Metadata::empty(),
            new EventV1WasOccurred()
        )));

        $messages = [];

        $store->visitEvents(new Criteria\AndX(new Criteria\EqId(new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac'))), new CallableEventVisitor(
            static function (DomainMessage $message) use (&$messages): void {
                $messages[] = $message;
            }
        ));

        $event = $messages[0]->payload;

        // visitEvents upcasts just like load: the V1 event is seen as V2.
        self::assertInstanceOf(EventV2WasOccurred::class, $event);
    }
}
