<?php

Route::middleware('auth:sanctum')->group(function () {
   require __DIR__ . '/ticket.php';
});
