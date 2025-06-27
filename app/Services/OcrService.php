namespace App\Services;

use Spatie\PdfToText\Pdf;
use thiagoalessio\TesseractOCR\TesseractOCR;

class OcrService
{
    public static function extractText(string $path): string
    {
        if (str_ends_with($path, '.pdf')) {
            return Pdf::getText($path);
        }

        return (new TesseractOCR($path))->run();
    }
}
