<?php

namespace Laramate\Support\Tasks;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laramate\Support\Tasks\Interfaces\ActionInterface;
use Laramate\Support\Traits\Makeable;

abstract class Action implements ActionInterface, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Makeable;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     */
    abstract public function handle();
}