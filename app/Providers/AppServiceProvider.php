<?php

namespace App\Providers;

use App\Models\Comment;
use App\Policies\CommentPolicy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Comment::class =>CommentPolicy::class,     
     ];
    /**
     * Register any application services.
     */
    public function register(): void
    {
       
    }

    
    public function boot(): void
    {
        //
    }
}
