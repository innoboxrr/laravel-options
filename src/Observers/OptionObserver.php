<?php
 
namespace Innoboxrr\LaravelOptions\Observers;
 
use Innoboxrr\LaravelOptions\Models\Option;
 
class OptionObserver
{

    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    // public $afterCommit = true;

    /**
     * Handle the Option "created" event.
     */
    public function created(Option $option): void
    {
        // Remove if laravel-audit is used: $option->log('created');
    }
 
    /**
     * Handle the Option "updated" event.
     */
    public function updated(Option $option): void
    {
        // Remove if laravel-audit is used: $option->log('updated');
    }
 
    /**
     * Handle the Option "deleted" event.
     */
    public function deleted(Option $option): void
    {
        // Remove if laravel-audit is used: $option->log('deleted');
    }
 
    /**
     * Handle the Option "restored" event.
     */
    public function restored(Option $option): void
    {
        // Remove if laravel-audit is used: $option->log('restored');
    }
 
    /**
     * Handle the Option "forceDeleted" event.
     */
    public function forceDeleted(Option $option): void
    {
        // Remove if laravel-audit is used: $option->log('forceDeleted');
    }
}