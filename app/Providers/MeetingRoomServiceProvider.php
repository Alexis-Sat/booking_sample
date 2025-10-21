<?php

namespace App\Providers;

use App\Interfaces\AuthInterface;
use App\Interfaces\BookingRepositoryInterface;
use App\Interfaces\MeetingRoomRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\BookingRepository;
use App\Repositories\MeetingRoomRepository;
use App\Repositories\UserRepository;
use App\Services\MeetingRoomService;
use Illuminate\Support\ServiceProvider;

class MeetingRoomServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BookingRepositoryInterface::class, BookingRepository::class);
        $this->app->bind(MeetingRoomRepositoryInterface::class, MeetingRoomRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
