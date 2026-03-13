<?php

namespace App\Traits;

use Illuminate\Foundation\Bus\PendingDispatch;

trait Dispatchable
{
    /**
     * Dispatch the job.
     *
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    public static function dispatch()
    {
        return new PendingDispatch(new static(...func_get_args()));
    }

    /**
     * Dispatch a command after the current process is finished.
     *
     * @return void
     */
    public static function dispatchAfterResponse()
    {
        return app('queue')->laterOn(
            null, 0, new static(...func_get_args())
        );
    }
}