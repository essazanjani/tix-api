<?php

use App\Http\Controllers\User\TicketController;

Route::group(['prefix' => 'ticket', 'as' => 'ticket.'], function ($router) {
    $router->get('', [TicketController::class, 'index'])->name('index');
    $router->get('{ticket}', [TicketController::class, 'show'])->name('show');
    $router->post('', [TicketController::class, 'store'])->name('store');
    $router->post('{ticket}', [TicketController::class, 'reply'])->name('reply');
});
