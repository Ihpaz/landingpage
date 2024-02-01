<?php

namespace App\Helpers;

use Spatie\Activitylog\Models\Activity;

class ActivityLog
{
    public static function create($model, $message = null)
    {
        activity('Create')
            ->performedOn($model)
            ->log($message ?? 'Add new data');
    }

    public static function update($model, $message = null)
    {
        activity('Update')
            ->performedOn($model)
            ->log($message ?? 'Update data');
    }

    public static function delete($model, $message = null)
    {
        activity('Delete')
            ->performedOn($model)
            ->log($message ?? 'Delete data');
    }

    public static function login()
    {
        activity('Login')
            ->log(':causer.fullname has been login');
    }

    public static function logout()
    {
        activity('Logout')
            ->log(':causer.fullname has been logout');
    }

    public static function log($title, $message)
    {
        activity($title)
            ->log($message);
    }

    public static function logCauser($title, $message, $user)
    {
        activity($title)
            ->causedBy($user)
            ->log($message);
    }

    public static function logPerfomed($title, $message, $model)
    {
        activity($title)
            ->performedOn($model)
            ->log($message);
    }

    public static function logPerfomedWithProperty($title, $message, $model, $property)
    {
        activity($title)
            ->performedOn($model)
            ->withProperty('attributes', $property)
            ->log($message);
    }

    public static function logPerfomedWithOldProperty($title, $message, $model, $property, $old)
    {
        activity($title)
            ->performedOn($model)
            ->withProperty('attributes', $property)
            ->withProperty('old', $old)
            ->log($message);
    }

    public static function logCauserPerformed($title, $message, $user, $model)
    {
        activity($title)
            ->performedOn($model)
            ->causedBy($user)
            ->log($message);
    }

    public static function logCauserWithProperty($title, $message, $user, $property)
    {
        activity($title)->causedBy($user)
            ->withProperty('attributes', $property)
            ->log($message);
    }

    public static function logWithProperty($title, $message, $property)
    {
        activity($title)
            ->withProperty('attributes', $property)
            ->log($message);
    }

    public static function logWithOldProperty($title, $message, $property, $old)
    {
        activity($title)
            ->withProperty('attributes', $property)
            ->withProperty('old', $old)
            ->log($message);
    }

    public static function impersonate($impersonateUser)
    {
        activity('Impersonate')
            ->log(':causer.fullname has been impersonate user ' . $impersonateUser . '');
    }

    public static function sentry($exception)
    {
        if (app()->bound('sentry')) {
            app('sentry')->captureException($exception);
        }
    }
}
