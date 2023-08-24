<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ResponseService;

class ResponseServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('customresponse', function () {
            return new ResponseService();
        });
    }
}
