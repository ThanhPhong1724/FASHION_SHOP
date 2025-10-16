<?php

namespace App\Console\Commands;

use App\Models\Wishlist;
use Illuminate\Console\Command;

class CleanupOrphanedWishlists extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wishlist:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up orphaned wishlist items (products that no longer exist)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning up orphaned wishlist items...');
        
        // Delete wishlist items where the product no longer exists
        $deletedCount = Wishlist::whereDoesntHave('product')->delete();
        
        $this->info("Cleaned up {$deletedCount} orphaned wishlist items.");
        
        return Command::SUCCESS;
    }
}
