<?php $pageTitle = 'Tambah Template Surat Baru'; ?>
<?php ob_start(); ?>

<div class="row g-4" data-aos="fade-up">
    <!-- Form Form -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <i class="bi bi-file-earmark-plus-fill me-2 text-primary"></i>Buat Template Surat Baru
            </div>
            <div class="card-body">
                <form method="POST" action="<?= APP_URL ?>/admin/surat/templatestore" id="templateForm" enctype="multipart/form-data">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold small">Nama Surat <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" placeholder="Contoh: Surat Keterangan Usaha" required maxlength="100">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Kode Unik <span class="text-danger">*</span></label>
                            <input type="text" name="kode" class="form-control text-uppercase" placeholder="Contoh: SKU" required maxlength="10">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Deskripsi Singkat</label>
                        <textarea name="deskripsi" class="form-control" rows="2" placeholder="Tulis deskripsi kegunaan surat..." maxlength="500"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Estimasi Pengerjaan (Hari) <span class="text-danger">*</span></label>
                        <input type="number" name="estimasi_hari" class="form-control" value="3" min="1" max="30" required style="max-width: 150px">
                    </div>

                    <!-- Persyaratan List -->
                    <div class="mb-4 border rounded p-3 bg-light">
                        <label class="form-label fw-semibold small d-flex justify-content-between align-items-center">
                            <span>Berkas Persyaratan Administrasi</span>
                            <button type="button" class="btn btn-xs btn-outline-primary" onclick="addRequirement()">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Persyaratan
                            </button>
                        </label>
                        <div id="requirementsContainer" class="d-flex flex-column gap-2 mt-2">
                            <div class="input-group input-group-sm requirement-item">
                                <input type="text" name="persyaratan[]" class="form-control" placeholder="Contoh: Fotokopi KTP" value="Fotokopi KTP">
                                <button class="btn btn-outline-danger" type="button" onclick="removeRequirement(this)"><i class="bi bi-trash"></i></button>
                            </div>
                            <div class="input-group input-group-sm requirement-item">
                                <input type="text" name="persyaratan[]" class="form-control" placeholder="Contoh: Fotokopi Kartu Keluarga" value="Fotokopi Kartu Keluarga">
                                <button class="btn btn-outline-danger" type="button" onclick="removeRequirement(this)"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    </div>

                    <!-- File Template Upload -->
                    <div class="mb-4 border rounded p-3 bg-light">
                        <label class="form-label fw-semibold small text-dark d-block">File Template Dokumen (Word/PDF) <span class="text-danger">*</span></label>
                        <input type="file" name="template_file" class="form-control" accept=".doc,.docx,.pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf" required>
                        <small class="text-muted mt-1 d-block" style="font-size: .7rem">Format: Word (.doc, .docx) atau PDF. Maksimal file: 5MB.</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary fw-bold px-4 py-2" id="submitBtn">
                            <i class="bi bi-save me-1"></i> Simpan Template
                        </button>
                        <a href="<?= APP_URL ?>/admin/surat/templates" class="btn btn-light fw-bold px-4 py-2">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Petunjuk Pembuatan Template -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white py-3">
                <i class="bi bi-info-circle-fill me-2 text-primary"></i>Petunjuk File Template
            </div>
            <div class="card-body" style="font-size: .82rem">
                <p class="text-muted">Siapkan dokumen Word (.docx) atau PDF yang berisi format standar surat administrasi desa Anda.</p>
                <p class="text-muted">Warga akan mengunduh file ini saat mengajukan surat, mengisi data diri mereka secara mandiri menggunakan aplikasi pengolah kata di komputer/ponsel mereka, kemudian mengunggah kembali file yang telah diisi ke sistem.</p>
                
                <hr class="my-3">
                <div class="alert alert-info border-0 p-2 mb-0" style="font-size: .75rem">
                    <i class="bi bi-lightbulb-fill me-1"></i>
                    <strong>Tips:</strong> Kosongkan bagian isian data diri warga (misalnya dengan memberi garis bawah <code>_______</code> atau tanda kurung siku <code>[ Nama ]</code>) agar memudahkan warga untuk mengisi data secara manual.
                </div>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php
$footerExtra = <<<JS
<script>
function addRequirement() {
    const container = document.getElementById('requirementsContainer');
    const div = document.createElement('div');
    div.className = 'input-group input-group-sm requirement-item';
    div.innerHTML = `
        <input type="text" name="persyaratan[]" class="form-control" placeholder="Contoh: Berkas Pendukung">
        <button class="btn btn-outline-danger" type="button" onclick="removeRequirement(this)"><i class="bi bi-trash"></i></button>
    `;
    container.appendChild(div);
}

function removeRequirement(button) {
    button.closest('.requirement-item').remove();
}

document.getElementById('templateForm').addEventListener('submit', function() {
    document.getElementById('submitBtn').disabled = true;
});
</script>
JS;
?>

<?php require VIEW_PATH . '/layouts/admin.php'; ?>
