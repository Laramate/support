<?php

namespace Laramate\Support\Tasks\Interfaces;

interface ActionInterface {
    /**
     * Execute the job.
     */
    public function handle();
}