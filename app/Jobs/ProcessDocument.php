<?php

namespace App\Jobs;

use App\Services\AiService;
use App\Services\OcrService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class ProcessDocument implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $filePath = Storage::path($this->invoice->file_path);
        $ocrText = OcrService::extractText($filePath);

        $data = AiService::extractInvoiceData($ocrText);

        $this->invoice->update([
            'raw_text' => $ocrText,
            'invoice_number' => $data['invoice_number'] ?? null,
            'invoice_date' => $data['invoice_date'] ?? null,
            'vendor_name' => $data['vendor_name'] ?? null,
            'total_amount' => $data['total_amount'] ?? null,
            'line_items' => json_encode($data['line_items'] ?? []),
        ]);
    }
}
