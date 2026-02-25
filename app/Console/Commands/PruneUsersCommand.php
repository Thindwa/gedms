<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class PruneUsersCommand extends Command
{
    protected $signature = 'edms:prune-users {--force : Skip confirmation}';
    protected $description = 'Delete all users except System Administrator (system.admin@edms.gov.mw)';

    public function handle(): int
    {
        if (!$this->option('force') && !$this->confirm('Delete all users except system.admin@edms.gov.mw?')) {
            return 0;
        }

        $count = User::where('email', '!=', 'system.admin@edms.gov.mw')->count();
        User::where('email', '!=', 'system.admin@edms.gov.mw')->delete();

        $this->info("Deleted {$count} user(s). System Administrator retained.");
        return 0;
    }
}
