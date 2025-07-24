<?php

declare(strict_types=1);

use App\Http\Controllers\Webhooks\CloudSignWebhookController;

Route::prefix('webhooks')->group(function () {
    Route::post('/cloudsign/contracts', [CloudSignWebhookController::class, 'handle']);
});
