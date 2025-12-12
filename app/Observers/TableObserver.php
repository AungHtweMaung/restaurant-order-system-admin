<?php

namespace App\Observers;

use App\Models\Table;
use App\Services\TableQrService;
use Illuminate\Support\Str;

class TableObserver
{
    public function __construct(public TableQrService $tableQrService)
    {
        //
    }


    /**
     * Handle the Table "creating" event.
     */
    public function creating(Table $table): void
    {
        $table->slug = Str::slug("table-{$table->table_number}");
        $table->qr_token = Str::random(15);
    }


    /**
     * Handle the Table "created" event.
     */
    public function created(Table $table): void
    {
        $this->tableQrService->generateQrCode($table);
    }

    /**
     * Handle the Table "updating" event.
     */
    public function updating(Table $table): void
    {
        if ($table->isDirty('table_number')) {
            $table->slug = Str::slug("table-{$table->table_number}");
            $table->qr_token = Str::random(15);
        }
    }

    /**
     * Handle the Table "updated" event.
     */
    public function updated(Table $table): void
    {
        $this->tableQrService->regenerateQr($table);
    }

    /**
     * Handle the Table "deleted" event.
     */
    public function deleted(Table $table): void
    {
        //
    }

    /**
     * Handle the Table "restored" event.
     */
    public function restored(Table $table): void
    {
        //
    }

    /**
     * Handle the Table "force deleted" event.
     */
    public function forceDeleted(Table $table): void
    {
        //
    }
}
