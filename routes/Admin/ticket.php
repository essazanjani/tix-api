<?php

use App\Http\Controllers\Admin\TicketController;

Route::group(['prefix' => 'ticket', 'as' => 'ticket.'], function ($router) {
    $router->get('', [TicketController::class, 'index'])->name('index');
    $router->get('{ticket}', [TicketController::class, 'show'])->name('show');
    $router->post('{ticket}/reply', [TicketController::class, 'reply'])->name('reply');
    $router->patch('{ticket}/status', [TicketController::class, 'changeStatus'])->name('changeStatus');
});
