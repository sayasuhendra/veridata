<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use OpenAI;

class InvoiceReader extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'read:invoice {invoice=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get invoice data with openai help.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = storage_path('app/public/invoices/').$this->argument('invoice').'.png';
        $base64 = base64_encode(file_get_contents($file));
        $this->info($file);
        // $mimeType = $f->getMimeType();
        // $mimeType = Storage::mimeType(file_get_contents($file));
        // dd($mimeType);
        // $this->info($mimeType);
        //
        $data = self::extractInvoiceData($base64, 'image/png');
        // $data = json_decode(self::utf8ize($data));
        dd($data);
    }

    public static function extractInvoiceData($base64, $fileType)
    {
        $apiKey = getenv('OPENAI_API_KEY');
        if (! $apiKey) {
            throw new \Exception('OpenAI API key not set in environment variables.');
        }

        // Initialize OpenAI client with the API key
        $client = OpenAI::client($apiKey);
        $response = $client->chat()->create([
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

    public static function utf8ize($d)
    {
        if (is_array($d)) {
            foreach ($d as $k => $v) {
                $d[$k] = self::utf8ize($v);
            }
        } elseif (is_object($d)) {
            // Recursively convert object properties
            $vars = get_object_vars($d);
            foreach ($vars as $k => $v) {
                $d->$k = self::utf8ize($v);
            }
        } elseif (is_string($d)) {
            // Try a list of common encodings before converting
            $encodings = ['UTF-8', 'ISO-8859-1', 'Windows-1251', 'Windows-1252', 'GB2312', 'BIG5'];
            foreach ($encodings as $encoding) {
                if (mb_check_encoding($d, $encoding)) {
                    return mb_convert_encoding($d, 'UTF-8', $encoding);
                }
            }

            // Use 'auto' as a last resort
            return mb_convert_encoding($d, 'UTF-8', 'auto');
        }

        return $d;
    }
}
