<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\ActivityLogger;
use App\Models\SystemUser;

trait LogsActivity
{
    protected static function bootLogsActivity(): void
    {
        foreach(['created', 'updated', 'deleted'] as $event){
            static::$event(function($model) use ($event){

                //Try authenticated user
                $actor = Auth::user() ?? SystemUser::actor('cli');

                //If no authenticated user, check for an explicit system user context set in the container
                if(!$actor && app()->has('system_user_context')){
                    $actor = app('system_user_context');
                }

                //If no actor and running in console, resolve the CLI system user
                if(!$actor && app()->runningInConsole()){
                    $actor = Systemuser::actor('cli');
                }

                //Fallback: try config-provided system user key
                if(!$actor && config('activitylogger.fallback_system_key')){
                    $actor = SystemUser::where('key', config('activitylogger.fallback_system_key'))->first();
                }
                
                DB::afterCommit(function() use ($actor, $model, $event){
                    try{
                        ActivityLogger::log($actor, $model, $event);
                    } catch (\Throwable $e){
                        Log::warning("ActivityLogger failed in {$model->getTable()}::{$event}: {$e->getMessage()}");
                    }
                });
            });
        }
    }
}
