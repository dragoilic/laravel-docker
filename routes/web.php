<?php

use App\Http\Controllers\App\View\AppController;
use App\Http\Controllers\App\View\RegisterController;
use App\Http\Controllers\App\View\ResetPasswordController;
use App\Http\Controllers\Backstage\View\BookController;
use App\Http\Controllers\Backstage\View\ConfigController;
use App\Http\Controllers\Backstage\Api\SignInController as BackstageSignInController;
use App\Http\Controllers\Backstage\View\HomeController as BackstageHomeController;
use App\Http\Controllers\Backstage\View\TournamentController as BackstageTournamentController;
use App\Http\Controllers\Backstage\View\PrizeController as BackstagePrizeController;
use App\Http\Controllers\Backstage\View\AdminController as BackstageUserController;
use App\Http\Controllers\Backstage\View\TournamentDashboardController;
use App\Http\Controllers\Backstage\View\WithdrawalController;
use App\Http\Controllers\Backstage\View\UserController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Broadcast;

use App\Http\Controllers\App\Api\FacebookAuthController;
use App\Http\Controllers\App\Api\GoogleAuthController;

/** @var Router $router */

$app = env('APP_URL_DOMAIN');
$backstage = env('BACKSTAGE_URL_SUBDOM') . '.' . env('APP_URL_DOMAIN');

Route::get('/api/facebook/redirect', FacebookAuthController::class . '@redirect');
Route::get('/api/facebook/callback', FacebookAuthController::class . '@callback');

Route::get('/api/google/redirect',  GoogleAuthController::class . '@redirect');
Route::get('/api/google/callback', GoogleAuthController::class . '@callback');

$router->domain($app)->group(function (Router $router) {
    $router->get('/{any}', AppController::class . '@index')->where('any', '.*');
    $router->get('/lobby', AppController::class . '@toLobby')->name('lobby');
    $router->get('/register', RegisterController::class . '@showRegistrationForm')->name('register');
    $router->get('/referralLink', RegisterController::class . '@showReferralLinkForm')->name('referralLink');
    $router->get('/resetpassword', ResetPasswordController::class . '@reset_password')->name('password.reset');
});

$router->domain($backstage)->group(function (Router $router) {
    $router->middleware('auth:backstage')->group(function (Router $router) {
        $router->get('/', BackstageHomeController::class . '@index')->name('backstage.home');
        $router->get('/config', ConfigController::class . '@show')->name('config.show');
        $router->get('/config/edit', ConfigController::class . '@edit')->name('config.edit');
        $router->put('/config', ConfigController::class . '@update')->name('config.update');

        // $router->get('/prizes', BackstagePrizeController::class . '@index')->name('prizes.index');
        // $router->get('/prizes/show', BackstagePrizeController::class . '@show')->name('prizes.show');
        // $router->get('/prizes/edit', BackstagePrizeController::class . '@edit')->name('prizes.edit');
        // $router->get('/prizes', BackstagePrizeController::class . '@create')->name('prizes.create');

        $router->resource('/prizes', BackstagePrizeController::class);

        $router->get('/tournaments/dashboard', TournamentDashboardController::class . '@index')->name('tournaments.dashboard');
        $router->post('/tournaments/{tournament}/check-complete', BackstageTournamentController::class . '@checkForCompletion');
        $router->post('/tournaments/{tournament}/grade-events', BackstageTournamentController::class . '@gradeEvents');
        $router->resource('/tournaments', BackstageTournamentController::class);

        $router->resource('/admins', BackstageUserController::class);

        $router->get('/book/active', BookController::class . '@active')->name('book.active');
        $router->post('/book/manage/{id}/cancel', BookController::class . '@cancel');
        $router->post('/book/manage/{id}/start', BookController::class . '@start');
        $router->post('/book/manage/{id}/finish', BookController::class . '@finish');
        $router->post('/book/manage/{id}/update', BookController::class . '@updateFixture');
        $router->post('/book/manage/{id}/updateSnapshot', BookController::class . '@updateSnapshot');

        $router->get('/users/export', UserController::class . '@export')->name('users.export');
        $router->get('/withdrawals/pending', WithdrawalController::class . '@pending')->name('withdrawals.pending');
        $router->post('/withdrawals/{id}/process', WithdrawalController::class . '@process');

        $router
            ->post('/logout', BackstageSignInController::class . '@logout')
            ->name("backstage.logout");
    });

    $router->get('/signin', BackstageSignInController::class . '@showLoginForm');
    $router->post('/signin', BackstageSignInController::class . '@login')->name("backstage.signin");
});
