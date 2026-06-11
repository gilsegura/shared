<?php

declare(strict_types=1);

namespace Shared\Tests\Replaying;

use PHPUnit\Framework\TestCase;
use Shared\Criteria;
use Shared\Criteria\AndX;
use Shared\Criteria\EqId;
use Shared\Domain\DomainMessage;
use Shared\Domain\Uuid;
use Shared\EventStore\EventStoreManagerInterface;
use Shared\EventStore\EventVisitorInterface;
use Shared\Replaying\Replayer;

final class ReplayerTest extends TestCase
{
    public function test_must_delegate_replay_to_the_event_store(): void
    {
        $eventStore = new SpyEventStoreManager();
        $eventVisitor = new NullEventVisitor();
        $criteria = new AndX(new EqId(new Uuid('9db0db88-3e44-4d2b-b46f-9ca547de06ac')));

        $replayer = new Replayer($eventStore, $eventVisitor);
        $replayer->__invoke($criteria);

        self::assertSame($criteria, $eventStore->receivedCriteria);
        self::assertSame($eventVisitor, $eventStore->receivedVisitor);
    }
}

final class SpyEventStoreManager implements EventStoreManagerInterface
{
    public Criteria\AndX|Criteria\OrX|null $receivedCriteria = null;

    public ?EventVisitorInterface $receivedVisitor = null;

    #[\Override]
    public function visitEvents(AndX|Criteria\OrX $criteria, EventVisitorInterface $eventVisitor): void
    {
        $this->receivedCriteria = $criteria;
        $this->receivedVisitor = $eventVisitor;
    }
}

final class NullEventVisitor implements EventVisitorInterface
{
    #[\Override]
    public function __invoke(DomainMessage $message): void
    {
    }
}
