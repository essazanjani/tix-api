<?php

Route::middleware(['auth:sanctum', 'check-permission'])->group(['prefix' => 'admin', 'as' => 'admin.'], function () {
   require __DIR__ . '/ticket.php';
});
