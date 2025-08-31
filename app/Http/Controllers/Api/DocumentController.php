<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenAI\Laravel\Facades\OpenAI;
use App\Models\Invoice;

class DocumentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,pdf|max:5120',
        ]);

        $file = $request->file('file');
        $path = $file->store('invoices', 'public');

        $base64 = base64_encode(Storage::disk('public')->get($path));
        $mimeType = Storage::disk('public')->mimeType($path);

        $data = $this->extractInvoiceData($base64, $mimeType);
        $data = preg_replace('/^```json\s*(.*)\s*```$/s', '$1', $data);
        $data = json_decode($data, true);

        $invoice = Invoice::create([
            'vendor_name' => $data['vendor_name'] ?? null,
            'invoice_id' => $data['invoice_number'] ?? null,
            'invoice_date' => $data['invoice_date'] ?? null,
            'due_date' => $data['due_date'] ?? null,
            'total_amount' => $data['total_amount'] ?? null,
            'tax_amount' => $data['tax_amount'] ?? null,
            'currency' => $data['currency'] ?? null,
            'original_file_path' => $path,
        ]);

        return response()->json(['message' => 'Document processed successfully', 'invoice_id' => $invoice->id]);
    }

    public function getReport(Request $request, $period)
    {
        $query = Invoice::query();

        switch ($period) {
            case 'daily':
                $query->whereDate('created_at', today());
                break;
            case 'weekly':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'monthly':
                $query->whereMonth('created_at', now()->month);
                break;
            default:
                return response()->json(['message' => 'Invalid period specified'], 400);
        }

        $invoices = $query->get();

        $totalAmount = $invoices->sum('total_amount');
        $report = "Report for the {$period} period:\n";
        $report .= "Total Invoices: " . $invoices->count() . "\n";
        $report .= "Total Amount: " . number_format($totalAmount, 2) . "\n";

        return response()->json(['report' => $report]);
    }

    private function extractInvoiceData($base64, $fileType)
    {
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:$fileType;base64,$base64",
                            ],
                        ],
                        [
                            'type' => 'text',
                            'text' => "Extract structured data from this invoice file as JSON:\n\nReturn format:\n".
                        json_encode([
                            'invoice_number' => 'INV-001',
                            'invoice_date' => '2024-06-01',
                            'vendor_name' => 'ABC Supplies',
                            'total_amount' => '1234.56',
                            'line_items' => [
                                ['item' => 'Widget A', 'qty' => 2, 'price' => 500],
                                ['item' => 'Widget B', 'qty' => 1, 'price' => 234.56],
                            ],
                        ], JSON_PRETTY_PRINT),
                        ],
                    ],
                ],
            ],
        ]);

        return $response->choices[0]->message->content;
    }
}
