namespace App\Services;

use Google\Cloud\DocumentAI\V1\Client\DocumentProcessorServiceClient;
use Google\Cloud\DocumentAI\V1\ProcessRequest;

class DocumentAIService
{
    public function processInvoice(string $filePath, string $mimeType): ?array
    {
        try {
            $options = ['credentials' => base_path('google-credentials.json')];
            $processorName = 'projects/YOUR_PROJECT_ID/locations/YOUR_LOCATION/processors/YOUR_INVOICE_PROCESSOR_ID';

            $documentProcessorServiceClient = new DocumentProcessorServiceClient($options);

            $content = file_get_contents($filePath);

            $request = (new ProcessRequest())
                ->setName($processorName)
                ->getRawDocument()
                ->setContent($content)
                ->setMimeType($mimeType);

            $result = $documentProcessorServiceClient->processDocument($request);
            $document = $result->getDocument();

            // Konversi hasil dari AI ke array yang lebih sederhana
            $extractedData = [];
            foreach ($document->getEntities() as $entity) {
                // Normalisasi key, ganti spasi dengan underscore dan jadikan lowercase
                $key = strtolower(str_replace(' ', '_', $entity->getType()));
                $value = $entity->getMentionText();
                $extractedData[$key] = $value;
            }

            // Simpan juga response mentah untuk debug
            $extractedData['raw_response'] = json_decode($result->serializeToJsonString(), true);


            $documentProcessorServiceClient->close();

            return $extractedData;

        } catch (\Exception $e) {
            // Lakukan logging error
            report($e);
            return null;
        }
    }
}
