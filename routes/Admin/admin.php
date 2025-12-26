<?php

Route::middleware(['auth:sanctum', 'check-permission'])->group(function () {
   require __DIR__ . '/ticket.php';
});
