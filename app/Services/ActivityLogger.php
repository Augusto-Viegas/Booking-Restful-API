<?php
namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\SystemUser;

/**
 * This file has a config file. config->activitylogger.php
 */
class ActivityLogger
{
    protected static bool $enabled = true;

    /**
     * $actor can be:
     * - User or system instance
     * - string key (resolved to SystemUser)
     * - null (then fallback or skip)
     */
    public static function log(User|SystemUser|string $actor, $model, ?string $action = null, ?string $description = null): ?ActivityLog
    {
      if(!self::$enabled) return null;

      //Resolve actor if string key is provided
      if(is_string($actor)){
        $actor = SystemUser::where('key', $actor)->first();
      }

      //If no actor provided, try fallback system from config
      if(is_null($actor) && config('activitylogger.fallback_system_key')){
        $actor = SystemUser::where('key', config('activitylogger.fallback_system_key'))->first();
      }

      //Decide origin and foreign key
      $origin = 'user';
      $userId = null;
      $systemUserId = null;

      if($actor instanceof User){
        $userId = $actor->id;
        $origin = 'user';
      } elseif($actor instanceof SystemUser){
        $systemUserId = $actor->id;
        $origin = 'system';
      } else {

        //If still null, we don't log or you can choose to log with origin 'unknown'
        return null;
      }
      
      $entityType = is_object($model) ? class_basename($model) : (string) $model;
      $entityId = is_object($model) && isset($model->id) ? $model->id : null;

      $log = ActivityLog::create([
        'user_id' => $userId,
        'system_user_id' => $systemUserId,
        'origin' => $origin,
        'action' => $action,
        'entity_type' => $entityType,
        'entity_id' => $entityId,
        'description' => $description ?? ($actor instanceof User ? $actor->name : ($actor->name ?? $actor->key)) . " {$action} {$entityType} #{$entityId}",
      ]);

      return $log;
    }

    public static function setEnabled()
    {
        self::$enabled = true;
    }

    public static function setDisabled()
    {
        self::$enabled = false;
    }

}