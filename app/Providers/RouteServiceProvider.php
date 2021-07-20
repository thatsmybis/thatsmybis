<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $idPattern = '[0-9]+';
        $slugPattern = '-|[a-z0-9]+(\-[a-z0-9]+)*';

        // ID Patterns
        Route::patterns([
            'batchId' => $idPattern,
            'expansionId' => $idPattern,
            'guildId' => $idPattern,
            'id' => $idPattern,
            'itemId' => $idPattern,
            'memberId' => $idPattern,
            'raidId' => $idPattern,
            'raidGroupId' => $idPattern,
        ]);

        // Slug Patterns
        Route::patterns([
            'expansionSlug' => $slugPattern,
            'guildSlug' => $slugPattern,
            'instanceSlug' => $slugPattern,
            'itemSlug' => $slugPattern,
            'characterSlug' => $slugPattern,
            'userSlug' => $slugPattern,
            'raidSlug' => $slugPattern,
        ]);

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
        $this->mapGuildRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "guild" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapGuildRoutes()
    {
        Route::middleware('web')
            ->group(base_path('routes/guild.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }
}
