<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Auth; // Importando o Auth
use Inertia\Inertia; // Importando o Inertia

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
        Schema::defaultStringLength(191);

        // Abaixo foi criado para termos uma variável dentro do Inertia para que possamos
        // resgatar os dados  referente ao usuário logado e usuá-los dentro do componente Vue (através da variável $page)
        Inertia::share('auth.user', function(){
            return Auth::user();
        });
    }
}
