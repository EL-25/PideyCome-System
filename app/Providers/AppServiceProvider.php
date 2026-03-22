<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Pedido;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. FORZAR HTTPS EN RAILWAY
        // Esto evita el "Network Error" de Axios al intentar mezclar HTTP con HTTPS
        if (config('app.env') === 'production' || env('RAILWAY_ENVIRONMENT')) {
            URL::forceScheme('https');
        }

        // 2. COMPARTIR EL CONTEO DE NOTIFICACIONES
        // Esto hace que la variable $notificacionesCount esté disponible en la NAV 
        // sin tener que pasarla manualmente en cada método del controlador.
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $count = Pedido::where('user_id', Auth::id())
                               ->where('estado', 'despachada')
                               ->where('notificacion_leida', false)
                               ->count();
                $view->with('notificacionesCount', $count);
            }
        });
    }
}
