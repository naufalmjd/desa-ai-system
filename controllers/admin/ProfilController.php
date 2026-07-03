<?php

declare(strict_types=1);

namespace Controller\Admin;

use Core\Controller;
use Repository\PendudukRepository;

/**
 * ProfilController — Profil Admin Desa.
 */
final class ProfilController extends Controller
{
    public function index(): void
    {
        $userId = $this->authId();
        $pendudukRepo = new PendudukRepository($this->db);
        $penduduk = $pendudukRepo->findByUserId($userId);
        $user = $this->auth();

        $this->render('admin/profil/index', compact('penduduk', 'user'), 'admin');
    }
}
