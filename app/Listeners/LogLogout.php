<?php

namespace App\Listeners;

use App\Services\AuditService;
use Illuminate\Auth\Events\Logout;

class LogLogout
{
    public function __construct(
        protected AuditService $audit
    ) {}

    public function handle(Logout $event): void
    {
        if ($event->user) {
            $this->audit->log(
                'auth.logout',
                \App\Models\User::class,
                $event->user->id,
                null,
                ['email' => $event->user->email]
            );
        }
    }
}
