<?php

declare(strict_types=1);

namespace Controller\Warga;

use Core\Controller;
use Repository\PendudukRepository;

/**
 * ProfilController — Mengelola data profil warga.
 */
final class ProfilController extends Controller
{
    public function index(): void
    {
        $userId = $this->authId();
        $pendudukRepo = new PendudukRepository($this->db);
        $penduduk = $pendudukRepo->findByUserId($userId);
        $user = $this->auth();

        $this->render('warga/profil/index', compact('penduduk', 'user'), 'warga');
    }
}
