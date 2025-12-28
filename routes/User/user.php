<?php

Route::middleware('auth:sanctum')->group(['prefix' => 'user', 'as' => 'user.'], function () {
   require __DIR__ . '/ticket.php';
});
