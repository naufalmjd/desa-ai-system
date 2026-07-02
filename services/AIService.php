<?php

declare(strict_types=1);

namespace Service;

use RuntimeException;

/**
 * AIService — HTTP client ke Python FastAPI AI Server.
 * Menangani YOLOv8 Computer Vision dan Gemini Chatbot.
 */
final class AIService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(AI_SERVER_URL, '/');
        $this->apiKey  = AI_SERVER_KEY;
    }

    // ── Computer Vision (YOLOv8) ──────────────────────────────────────────

    /**
     * Kirim gambar ke AI server untuk deteksi objek YOLOv8.
     *
     * @param  string $imagePath Path absolut file gambar
     * @return array{kategori_deteksi:string, confidence_score:float, prioritas_ai:string, bounding_boxes:array, labels:array, processing_time:float}
     */
    public function detectImage(string $imagePath): array
    {
        if (!is_file($imagePath)) {
            throw new RuntimeException('File gambar tidak ditemukan: ' . $imagePath);
        }

        $response = $this->postFile('/api/v1/detect', $imagePath);

        return [
            'kategori_deteksi' => $response['category']          ?? 'Tidak Teridentifikasi',
            'confidence_score' => (float)($response['confidence'] ?? 0),
            'prioritas_ai'     => $this->mapPrioritas((float)($response['confidence'] ?? 0)),
            'bounding_boxes'   => $response['boxes']             ?? [],
            'labels'           => $response['labels']            ?? [],
            'processing_time'  => (float)($response['processing_time_ms'] ?? 0),
            'raw_response'     => $response,
        ];
    }

    // ── Chatbot (Gemini) ──────────────────────────────────────────────────

    /**
     * Kirim pesan ke AI server untuk jawaban Gemini.
     *
     * @param  string $message  Pesan dari user
     * @param  array  $history  Riwayat percakapan [{role, content}]
     * @param  string $context  Konteks sistem desa
     */
    public function chat(string $message, array $history = [], string $context = ''): array
    {
        $payload = [
            'message' => $message,
            'history' => $history,
            'context' => $context ?: $this->getDesaContext(),
        ];

        try {
            $response = $this->post('/api/v1/chat', $payload);
            return [
                'success'  => true,
                'reply'    => $response['reply']  ?? 'Maaf, terjadi kesalahan pada AI server.',
                'tokens'   => $response['tokens'] ?? 0,
            ];
        } catch (\Throwable $e) {
            $reply = $this->getLocalMockReply($message);
            return [
                'success' => true,
                'reply'   => $reply,
                'tokens'  => 0,
            ];
        }
    }

    // ── Sentiment Analysis ────────────────────────────────────────────────

    public function analyzeSentiment(string $text): array
    {
        $response = $this->post('/api/v1/sentiment', ['text' => $text]);
        return [
            'label' => $response['label'] ?? 'neutral',
            'score' => (float)($response['score'] ?? 0.5),
        ];
    }

    // ── HTTP Helpers ──────────────────────────────────────────────────────

    private function post(string $endpoint, array $payload): array
    {
        $ch = curl_init($this->baseUrl . $endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
                'Accept: application/json',
            ],
            CURLOPT_TIMEOUT        => AI_TIMEOUT,
            CURLOPT_SSL_VERIFYPEER => APP_ENV === 'production',
        ]);

        $body  = curl_exec($ch);
        $code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) throw new RuntimeException('AI server error: ' . $error);
        if ($code !== 200) throw new RuntimeException("AI server HTTP $code: $body");

        return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
    }

    private function postFile(string $endpoint, string $filePath): array
    {
        $ch = curl_init($this->baseUrl . $endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => [
                'file' => new \CURLFile($filePath),
            ],
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->apiKey,
            ],
            CURLOPT_TIMEOUT        => AI_TIMEOUT,
        ]);

        $body  = curl_exec($ch);
        $code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) throw new RuntimeException('AI server error: ' . $error);
        if ($code !== 200) throw new RuntimeException("AI server HTTP $code");

        return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
    }

    private function mapPrioritas(float $confidence): string
    {
        return match(true) {
            $confidence >= 90 => 'kritis',
            $confidence >= 75 => 'tinggi',
            $confidence >= 55 => 'sedang',
            default           => 'rendah',
        };
    }

    private function getDesaContext(): string
    {
        return sprintf(
            'Kamu adalah asisten AI pelayanan publik %s, %s, %s, %s. '
            . 'Bantu warga dengan informasi layanan surat, pengaduan, bantuan sosial, '
            . 'dan prosedur administrasi desa. Jawab dalam Bahasa Indonesia yang sopan dan jelas.',
            DESA_NAMA, DESA_KEC, DESA_KAB, DESA_PROV
        );
    }

    private function getLocalMockReply(string $message): string
    {
        $msg = strtolower($message);
        
        if (str_contains($msg, 'halo') || str_contains($msg, 'hai') || str_contains($msg, 'pagi') || str_contains($msg, 'siang') || str_contains($msg, 'sore') || str_contains($msg, 'malam') || str_contains($msg, 'permisi')) {
            return "Halo! Saya **SIAP-Bot**, asisten virtual pintar Desa Sukamaju. 🤖✨\n\nSaya dapat membantu Anda dengan berbagai informasi berikut:\n1. 📂 **Pengajuan Surat**: Panduan & persyaratan surat administrasi.\n2. 📢 **Pengaduan**: Cara melapor keluhan fasilitas umum/lingkungan.\n3. 🤝 **Bantuan Sosial (Bansos)**: Program bantuan yang aktif saat ini.\n\nAda yang bisa saya bantu hari ini?";
        }
        
        if (str_contains($msg, 'surat') || str_contains($msg, 'syarat') || str_contains($msg, 'administrasi') || str_contains($msg, 'pengantar')) {
            return "Untuk mengajukan surat administrasi di **SIAP-Desa**, silakan ikuti langkah berikut:\n\n1. Buka menu **Pengajuan Surat** di sidebar kiri Anda.\n2. Klik tombol **Buat Pengajuan**.\n3. Pilih Jenis Surat yang dibutuhkan (misal: *Surat Keterangan Tidak Mampu (SKTM)* atau *Surat Pengantar Domisili*).\n4. Unggah berkas persyaratan wajib (format PDF/JPG maks. 5MB) seperti **Fotokopi KTP** & **KK**.\n5. Kirim pengajuan dan Anda dapat memantaunya di menu **Tracking Surat**.\n\n*Catatan: Seluruh surat akan ditandatangani secara digital (QR Code) oleh Kepala Desa.*";
        }
        
        if (str_contains($msg, 'aduan') || str_contains($msg, 'pengaduan') || str_contains($msg, 'lapor') || str_contains($msg, 'keluh')) {
            return "Jika Anda menemukan masalah fasilitas umum (seperti jalan berlubang, sampah menumpuk, atau lampu jalan mati), Anda dapat melaporkannya melalui menu **Pengaduan**:\n\n1. Pilih menu **Pengaduan** -> **Buat Laporan**.\n2. Tuliskan judul, deskripsi lengkap, alamat kejadian, serta unggah **Foto Bukti**.\n3. Model AI YOLOv8 kami akan mendeteksi objek foto secara otomatis dan menentukan tingkat prioritas (*Kritis, Tinggi, Sedang, Rendah*) agar staf desa segera menindaklanjutinya.";
        }
        
        if (str_contains($msg, 'bansos') || str_contains($msg, 'bantuan') || str_contains($msg, 'blt') || str_contains($msg, 'pkh')) {
            return "Saat ini di Desa Sukamaju terdapat beberapa program **Bantuan Sosial (Bansos)** aktif:\n\n* **BLT Dana Desa**: Bantuan tunai bagi keluarga pra-sejahtera sasaran ekstrem.\n* **PKH (Program Keluarga Harapan)**: Bantuan untuk kesehatan keluarga, ibu hamil, dan anak sekolah.\n* **BPNT (Bantuan Pangan Non-Tunai)**: Bantuan bahan pangan pokok bulanan.\n\nAnda dapat melihat daftar lengkap program bansos ini di menu **Informasi Desa**.";
        }
        
        if (str_contains($msg, 'kades') || str_contains($msg, 'lurah') || str_contains($msg, 'pimpinan') || str_contains($msg, 'nama kepala')) {
            return "Kepala Desa Sukamaju saat ini dijabat oleh **H. Ahmad Fauzi**. Beliau berkomitmen mewujudkan pelayanan desa yang transparan, cerdas (*Smart Village*), dan responsif berbasis teknologi digital.";
        }
        
        return "Terima kasih atas pesan Anda! Saya memahami pertanyaan Anda mengenai layanan di Desa Sukamaju.\n\nSilakan kunjungi menu terkait di sidebar kiri:\n* 📂 **Pengajuan Surat** untuk administrasi surat.\n* 📢 **Pengaduan** untuk melaporkan keluhan lingkungan.\n* 🌐 **Informasi Desa** untuk pengumuman & bansos.\n\nAtau Anda dapat merumuskan kembali pertanyaan Anda dengan kata kunci seperti *'syarat surat'*, *'cara lapor aduan'*, atau *'program bansos'* agar saya dapat membantu secara lebih spesifik.";
    }
}
