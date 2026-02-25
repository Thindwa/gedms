<?php

namespace App\Listeners;

use App\Services\AuditService;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function __construct(
        protected AuditService $audit
    ) {}

    public function handle(Login $event): void
    {
        $this->audit->log(
            'auth.login',
            \App\Models\User::class,
            $event->user->id,
            null,
            ['email' => $event->user->email]
        );
    }
}
