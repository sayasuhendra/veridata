namespace App\Services;
use OpenAI\Laravel\Facades\OpenAI;

public static function extractInvoiceData(string $rawText): array
{
    $response = OpenAI::chat()->create([
        'model' => 'gpt-4o',
        'messages' => [
            ['role' => 'user', 'content' =>
                "Extract structured data from this invoice text as JSON:\n\n$rawText\n\n\nReturn format:\n" .
                json_encode([
                    "invoice_number" => "INV-001",
                    "invoice_date" => "2024-06-01",
                    "vendor_name" => "ABC Supplies",
                    "total_amount" => "1234.56",
                    "line_items" => [
                        ["item" => "Widget A", "qty" => 2, "price" => 500],
                        ["item" => "Widget B", "qty" => 1, "price" => 234.56]
                    ]
                ], JSON_PRETTY_PRINT)
            ]
        ]
    ]);

    return json_decode($response->choices[0]->message->content, true);
}
