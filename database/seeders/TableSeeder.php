<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\Table;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd; // <- Use GD backend
use BaconQrCode\Writer;
use Illuminate\Support\Str;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        $this->cleanupOldQrCodes();

        $this->command->info('Starting tables and QR code generation...');

        for ($i = 1; $i <= 10; $i++) {
            $this->createTableWithQrCode($i);
        }

        $this->command->info('All tables and QR codes generated successfully!');
    }

    private function createTableWithQrCode(int $tableNumber): void
    {
        $slug = Str::slug("table-{$tableNumber}");
        $qrToken = $this->generateUniqueToken();

        $table = Table::create([
            'table_number' => (string) $tableNumber,
            'slug' => $slug,
            'qr_token' => $qrToken,
            'qr_code_path' => null,
        ]);

        $qrInfo = $this->generateQrCode($table);

        $this->command->info("Table {$tableNumber} created - QR: {$qrInfo['qr_url']}");
    }

    private function generateUniqueToken(): string
    {
        return Str::random(16);
    }

    private function generateQrCode(Table $table): array
    {
        $url = $this->generateTableUrl($table);

        // <- Use GD backend instead of Imagick
        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        $qrCodeContent = $writer->writeString($url);

        $directory = 'qr-codes/tables';
        $filename = "table-{$table->table_number}-{$table->qr_token}.svg";
        $path = "{$directory}/{$filename}";

        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory, 0755, true);
        }

        Storage::disk('public')->put($path, $qrCodeContent);

        $table->update(['qr_code_path' => $path]);

        return [
            'path' => $path,
            'url' => Storage::url($path),
            'qr_url' => $url,
            'table_id' => $table->id,
            'table_number' => $table->table_number,
        ];
    }

    private function generateTableUrl(Table $table): string
    {
        $frontendUrl = config('frontend.url', 'http://localhost:3000');
        return "{$frontendUrl}/table/{$table->slug}/{$table->qr_token}";
    }

    private function cleanupOldQrCodes(): void
    {
        if (Storage::disk('public')->exists('qr-codes/tables')) {
            $files = Storage::disk('public')->files('qr-codes/tables');
            if (count($files) > 0) {
                Storage::disk('public')->delete($files);
                // $this->command->info('Cleaned up old QR code files.');
            }
        }
    }

    private function getQrCodePublicUrl(string $path): string
    {
        return Storage::url($path);
    }
}
