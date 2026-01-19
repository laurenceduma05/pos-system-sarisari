<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ChequeIssuances;
use Carbon\Carbon;

class UpdatePayablesDue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payables:update-due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically update payables due date and status at 5 PM';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today();
        $cheques = ChequeIssuances::where('due_date', $today->toDateString())
            ->where('status', '!=', 'Paid')
            ->get();

        foreach ($cheques as $cheque) {
            // Update due_date to tomorrow and set status to 'payables'
            $cheque->update([
                'due_date' => $today->addDay()->toDateString(), // set to tomorrow
                'status'   => 'Payables', //set status to payables
            ]);
        }

        $this->info('Payables updated successfully!');
        // return Command::SUCCESS;
    }
}
