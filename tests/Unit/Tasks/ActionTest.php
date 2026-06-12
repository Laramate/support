<?php

namespace Laramate\Support\Tests\Unit\Tasks;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Queue;
use Laramate\Support\Tasks\Action;
use Laramate\Support\Tasks\Interfaces\ActionInterface;
use Laramate\Support\Tests\TestCase;

class CalculateSumAction extends Action
{
    public static ?int $lastResult = null;

    public function __construct(
        public int $a,
        public int $b,
    ) {}

    public function handle(): int
    {
        return static::$lastResult = $this->a + $this->b;
    }
}

class ActionTest extends TestCase
{
    public function test_action_implements_action_interface()
    {
        $this->assertInstanceOf(ActionInterface::class, CalculateSumAction::make(1, 2));
    }

    public function test_action_is_queueable()
    {
        $this->assertInstanceOf(ShouldQueue::class, CalculateSumAction::make(1, 2));
        $this->assertContains(SerializesModels::class, class_uses_recursive(CalculateSumAction::class));
    }

    public function test_make_creates_instance_with_constructor_parameters()
    {
        $action = CalculateSumAction::make(1, 2);

        $this->assertSame(1, $action->a);
        $this->assertSame(2, $action->b);
    }

    public function test_handle_can_be_executed_directly()
    {
        $this->assertSame(3, CalculateSumAction::make(1, 2)->handle());
    }

    public function test_dispatch_sync_executes_immediately()
    {
        CalculateSumAction::$lastResult = null;

        CalculateSumAction::dispatchSync(3, 4);

        $this->assertSame(7, CalculateSumAction::$lastResult);
    }

    public function test_dispatch_pushes_action_to_queue()
    {
        Queue::fake();

        CalculateSumAction::dispatch(1, 2);

        Queue::assertPushed(
            CalculateSumAction::class,
            fn (CalculateSumAction $action) => $action->a === 1 && $action->b === 2
        );
    }

    public function test_dispatch_to_specific_queue()
    {
        Queue::fake();

        CalculateSumAction::dispatch(1, 2)->onQueue('reports');

        Queue::assertPushedOn('reports', CalculateSumAction::class);
    }

    public function test_dispatch_if_and_unless()
    {
        Queue::fake();

        CalculateSumAction::dispatchIf(false, 1, 2);
        CalculateSumAction::dispatchUnless(true, 1, 2);

        Queue::assertNothingPushed();

        CalculateSumAction::dispatchIf(true, 1, 2);

        Queue::assertPushed(CalculateSumAction::class, 1);
    }
}
