<?php

declare(strict_types=1);

namespace Controller\Kepaladesa;

use Core\Controller;
use Repository\PengaduanRepository;

/**
 * AiAnalyticsController — Panel pemantauan peta sebaran aduan YOLOv8.
 */
final class AiAnalyticsController extends Controller
{
    public function index(): void
    {
        $repo = new PengaduanRepository($this->db);
        $mapData = $repo->getMapData();
        
        $stats = [
            'total' => count($mapData),
            'kritis' => 0,
            'tinggi' => 0,
            'sedang' => 0,
            'rendah' => 0
        ];
        
        foreach ($mapData as $m) {
            $p = $m['prioritas'];
            if (isset($stats[$p])) {
                $stats[$p]++;
            } else {
                $stats['sedang']++;
            }
        }

        $user = $this->auth();
        $this->render('kepaladesa/ai-analytics/index', compact('mapData', 'stats', 'user'), 'kepaladesa');
    }
}
