<?php

namespace App\Providers;

use App\Models\Smtp;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        $smtp = Smtp::first();
        if(isset($smtp) && $smtp != false && $smtp['status'] == 1){

            if($smtp){
                $data =[
                    'driver' => 'smtp',
                    'host' => $smtp->host,
                    'port' => $smtp->port,
                    'encryption' => 'tls',
                    'username' => $smtp->user,
                    'password' => $smtp->pass,
                    'from' => [
                        'address' => $smtp->from_email,
                        'name' => $smtp->from_name
                    ]
                ];
                Config::set('mail',$data);
            }
        }
    }
}
