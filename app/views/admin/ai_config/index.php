<div class="container-fluid px-3 px-lg-4 py-4">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-gradient-primary rounded-pill px-3 py-1">
                    <i class="bi bi-robot me-1"></i>AI & Chatbot Intelligence
                </span>
                <span class="badge bg-secondary-subtle text-secondary border">
                    Role: <?= ucfirst(htmlspecialchars($data['userRole'] ?? 'Admin')); ?>
                </span>
            </div>
            <h2 class="h3 fw-bold text-dark mb-0">Konfigurasi Chatbot AI ADCom Jasa</h2>
            <p class="text-muted small mb-0">Kelola model Google Gemini, parameter inferensi, API Key rahasia, dan persona chatbot secara fleksibel tanpa mengedit source code.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= BASEURL; ?>/<?= ($data['userRole'] === 'operator') ? 'operator' : 'admin'; ?>" class="btn btn-outline-secondary rounded-pill px-3 btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Dashboard
            </a>
            <button type="button" class="btn btn-primary rounded-pill px-3 btn-sm" onclick="document.getElementById('formAiConfig').submit();">
                <i class="bi bi-check2-circle me-1"></i>Simpan Perubahan
            </button>
        </div>
    </div>

    <?php Flasher::flash(); ?>

    <!-- Alert Uji Koneksi Dinamis -->
    <div id="testResultAlert" class="alert d-none rounded-4 border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-start gap-3">
            <div id="testAlertIcon" class="fs-4"></div>
            <div class="flex-grow-1">
                <h6 id="testAlertTitle" class="fw-bold mb-1"></h6>
                <p id="testAlertMessage" class="small mb-0"></p>
                <div id="testAlertMeta" class="mt-2 text-xs opacity-75"></div>
            </div>
            <button type="button" class="btn-close" onclick="document.getElementById('testResultAlert').classList.add('d-none');"></button>
        </div>
    </div>

    <form id="formAiConfig" action="<?= BASEURL; ?>/aiConfig/update" method="POST">
        <?= CSRF::input(); ?>

        <div class="row g-4">
            <!-- Left Column: Model & API Key Settings -->
            <div class="col-lg-7">
                <!-- Card B: Pengaturan Model Gemini -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-shape icon-shape-sm bg-gradient-primary text-white rounded-3 d-flex align-items-center justify-content-center p-2">
                                <i class="bi bi-cpu text-white"></i>
                            </div>
                            <h5 class="fw-bold mb-0 text-dark">Pengaturan Model Google Gemini</h5>
                        </div>
                    </div>

                    <div class="card-body p-4 pt-2">
                        <!-- Provider & Master Switch -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted mb-1">AI Provider</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-google text-primary"></i>
                                    </span>
                                    <input type="text" class="form-control bg-light border-start-0" value="Google Gemini API (v1beta)" readonly>
                                </div>
                                <div class="form-text small" style="font-size: 11px;">Integrasi resmi Google Generative Language.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Status Chatbot AI</label>
                                <div class="p-2 px-3 rounded-3 border bg-light d-flex align-items-center justify-content-between">
                                    <span class="small fw-semibold text-dark">Aktifkan Layanan AI</span>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch" id="ai_enabled" name="ai_enabled" value="1" <?= (($data['settings']['ai_enabled'] ?? '1') == '1') ? 'checked' : ''; ?> style="width: 2.5rem; height: 1.3rem;">
                                    </div>
                                </div>
                                <div class="form-text small" style="font-size: 11px;">Jika nonaktif, chatbot akan merespons dengan pesan pemeliharaan atau FAQ.</div>
                            </div>
                        </div>

                        <!-- Gemini Model Selection -->
                        <div class="mb-4">
                            <label for="ai_model" class="form-label small fw-semibold text-dark mb-1">Pilihan Model Gemini</label>
                            <select class="form-select rounded-3 py-2" id="ai_model" name="ai_model" required>
                                <?php 
                                $selectedModel = $data['settings']['ai_model'] ?? 'gemini-flash-latest';
                                foreach ($data['availableModels'] as $modelKey => $mInfo): 
                                ?>
                                    <option value="<?= htmlspecialchars($modelKey); ?>" <?= ($selectedModel === $modelKey) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($mInfo['name']); ?> &mdash; [<?= htmlspecialchars($mInfo['badge']); ?>]
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div id="modelDescriptionBox" class="mt-2 p-2 px-3 rounded-3 bg-light border small text-muted">
                                <i class="bi bi-info-circle me-1 text-primary"></i>
                                <span id="modelDescriptionText">Model multimodal generasi terbaru dengan kecepatan tinggi dan latensi rendah.</span>
                            </div>
                        </div>

                        <!-- Numeric Parameters (Temperature, Max Tokens, Timeout) -->
                        <h6 class="text-uppercase text-xs fw-bold text-muted mb-3" style="letter-spacing: 0.5px;">Parameter Inferensi & Latensi</h6>

                        <!-- Temperature Slider & Number Input -->
                        <div class="p-3 rounded-3 border mb-3 bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label for="ai_temperature" class="form-label small fw-semibold text-dark mb-0">
                                    <i class="bi bi-thermometer-half text-danger me-1"></i>Temperature (Kreativitas Respons)
                                </label>
                                <span id="tempValueBadge" class="badge bg-primary rounded-pill px-3 py-1">
                                    <?= htmlspecialchars($data['settings']['ai_temperature'] ?? '0.7'); ?>
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <span class="small text-muted" style="font-size: 11px;">0.0 (Presisi)</span>
                                <input type="range" class="form-range flex-grow-1" id="ai_temperature_range" min="0.0" max="2.0" step="0.1" value="<?= htmlspecialchars($data['settings']['ai_temperature'] ?? '0.7'); ?>">
                                <span class="small text-muted" style="font-size: 11px;">2.0 (Kreatif)</span>
                                <input type="number" class="form-control text-center py-1" id="ai_temperature" name="ai_temperature" min="0.0" max="2.0" step="0.1" value="<?= htmlspecialchars($data['settings']['ai_temperature'] ?? '0.7'); ?>" style="width: 75px;" required>
                            </div>
                            <div class="form-text small mt-1" style="font-size: 11px;">
                                Nilai disarankan: <strong>0.3 - 0.7</strong> untuk asisten layanan jasa teknis agar jawaban tetap faktual dan akurat.
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="ai_max_tokens" class="form-label small fw-semibold text-dark">
                                    <i class="bi bi-card-text text-info me-1"></i>Maksimum Output Tokens
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="ai_max_tokens" name="ai_max_tokens" min="50" max="8192" step="50" required value="<?= htmlspecialchars($data['settings']['ai_max_tokens'] ?? '800'); ?>">
                                    <span class="input-group-text small bg-light">Tokens</span>
                                </div>
                                <div class="form-text text-muted small" style="font-size: 11px;">Rentang: 50 - 8192 (Bawaan: 800 tokens).</div>
                            </div>
                            <div class="col-md-6">
                                <label for="ai_timeout" class="form-label small fw-semibold text-dark">
                                    <i class="bi bi-stopwatch text-warning me-1"></i>Batas Waktu Request (Timeout)
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="ai_timeout" name="ai_timeout" min="5" max="60" step="1" required value="<?= htmlspecialchars($data['settings']['ai_timeout'] ?? '15'); ?>">
                                    <span class="input-group-text small bg-light">Detik</span>
                                </div>
                                <div class="form-text text-muted small" style="font-size: 11px;">Rentang: 5 - 60 detik (Bawaan: 15 detik).</div>
                            </div>
                        </div>

                        <!-- Fallback FAQ Switch -->
                        <div class="p-3 rounded-3 border bg-light">
                            <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0 mb-0">
                                <div>
                                    <label class="form-check-label fw-semibold text-dark mb-0 d-block" for="ai_fallback_faq">
                                        <i class="bi bi-shield-check text-success me-1"></i>Mode Fallback FAQ Otomatis
                                    </label>
                                    <small class="text-muted">Jika Gemini API mengalami kendala jaringan atau batas kuota terlampaui (429/503), otomatis berikan jawaban cerdas dari database katalog lokal.</small>
                                </div>
                                <input class="form-check-input ms-3" type="checkbox" role="switch" id="ai_fallback_faq" name="ai_fallback_faq" value="1" <?= (($data['settings']['ai_fallback_faq'] ?? '1') == '1') ? 'checked' : ''; ?>>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card C: Pengaturan API Key (Aman & Terisolasi) -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-shape icon-shape-sm bg-gradient-dark text-white rounded-3 d-flex align-items-center justify-content-center p-2">
                                <i class="bi bi-key text-white"></i>
                            </div>
                            <h5 class="fw-bold mb-0 text-dark">Kredensial & Keamanan Google Gemini API</h5>
                        </div>
                    </div>

                    <div class="card-body p-4 pt-2">
                        <!-- Secret Storage Callout -->
                        <div class="alert alert-light border rounded-3 p-3 mb-3 d-flex align-items-start gap-2">
                            <i class="bi bi-shield-lock-fill text-primary fs-5 mt-1"></i>
                            <div class="small text-muted">
                                <strong class="text-dark d-block mb-1">Penyimpanan Rahasia (Secret Storage) Terisolasi</strong>
                                API Key disimpan secara eksklusif dalam file <code>.env</code> server yang dilindungi dari akses publik via Web Server. Nilai raw API key <strong>tidak pernah</strong> disimpan dalam bentuk plaintext di database atau ditampilkan di antarmuka frontend.
                            </div>
                        </div>

                        <!-- Input API Key Baru -->
                        <div class="mb-3">
                            <label for="ai_api_key" class="form-label small fw-semibold text-dark d-flex justify-content-between align-items-center">
                                <span>Perbarui API Key Google Gemini</span>
                                <span class="badge <?= $data['hasApiKey'] ? 'bg-success-subtle text-success border border-success' : 'bg-warning-subtle text-warning border border-warning'; ?> rounded-pill px-2 py-1">
                                    <i class="bi <?= $data['hasApiKey'] ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill'; ?> me-1"></i>
                                    <?= $data['hasApiKey'] ? 'Tersimpan: ' . htmlspecialchars($data['maskedKey']) : 'Belum Dikonfigurasi'; ?>
                                </span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" class="form-control border-start-0 border-end-0" id="ai_api_key" name="ai_api_key" autocomplete="new-password" placeholder="<?= $data['hasApiKey'] ? 'API key telah dikonfigurasi (kosongkan jika tidak ingin mengubah)' : 'Masukkan AIzaSy... API Key Google Gemini Anda'; ?>">
                                <button class="btn btn-outline-secondary border-start-0" type="button" id="btnToggleApiKey" title="Tampilkan / Sembunyikan">
                                    <i class="bi bi-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                            <div class="form-text small" style="font-size: 11px;">
                                Dapatkan API Key secara gratis atau berbayar melalui <a href="https://aistudio.google.com/app/apikey" target="_blank" rel="noopener" class="text-primary text-decoration-none fw-semibold">Google AI Studio <i class="bi bi-box-arrow-up-right"></i></a>.
                            </div>
                        </div>

                        <!-- Tombol Uji Langsung Kredensial Ini -->
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" id="btnTestConnection">
                                <i class="bi bi-broadcast me-1" id="testBtnIcon"></i>
                                <span id="testBtnText">Uji Koneksi Gemini Sekarang</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Status & Chatbot Persona Settings -->
            <div class="col-lg-5">
                <!-- Card A: Status AI Realtime -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="icon-shape icon-shape-sm bg-gradient-success text-white rounded-3 d-flex align-items-center justify-content-center p-2">
                                    <i class="bi bi-activity text-white"></i>
                                </div>
                                <h5 class="fw-bold mb-0 text-dark">Status AI & Diagnostik</h5>
                            </div>
                            <span class="badge rounded-pill <?= (($data['settings']['ai_enabled'] ?? '1') == '1') ? 'bg-success' : 'bg-secondary'; ?> px-3 py-1">
                                <?= (($data['settings']['ai_enabled'] ?? '1') == '1') ? 'Layanan Aktif' : 'Non-Aktif'; ?>
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-4 pt-2">
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Status Integrasi Gemini</span>
                                <span class="badge rounded-pill <?= (($data['settings']['ai_enabled'] ?? '1') == '1') ? 'bg-success-subtle text-success border border-success' : 'bg-secondary-subtle text-secondary border'; ?>">
                                    <i class="bi <?= (($data['settings']['ai_enabled'] ?? '1') == '1') ? 'bi-check-circle-fill' : 'bi-dash-circle'; ?> me-1"></i>
                                    <?= (($data['settings']['ai_enabled'] ?? '1') == '1') ? 'Aktif' : 'Non-Aktif'; ?>
                                </span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Konfigurasi API Key</span>
                                <span class="badge rounded-pill <?= $data['hasApiKey'] ? 'bg-success-subtle text-success border border-success' : 'bg-danger-subtle text-danger border border-danger'; ?>">
                                    <?= $data['hasApiKey'] ? 'Tersedia (' . htmlspecialchars($data['maskedKey']) . ')' : 'Belum Dikonfigurasi'; ?>
                                </span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Model Terpasang</span>
                                <span class="fw-bold small text-dark" id="statusActiveModel">
                                    <?= htmlspecialchars($data['settings']['ai_model'] ?? 'gemini-flash-latest'); ?>
                                </span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Fallback Mode FAQ</span>
                                <span class="badge rounded-pill <?= (($data['settings']['ai_fallback_faq'] ?? '1') == '1') ? 'bg-info-subtle text-info border border-info' : 'bg-light text-muted border'; ?>">
                                    <?= (($data['settings']['ai_fallback_faq'] ?? '1') == '1') ? 'Siap Otomatis' : 'Dimatikan'; ?>
                                </span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Pengujian Terakhir</span>
                                <span class="small text-dark fw-semibold" id="statusLastTestAt">
                                    <?= !empty($data['settings']['last_test_at']) ? date('d M Y, H:i', strtotime($data['settings']['last_test_at'])) : 'Belum pernah diuji'; ?>
                                </span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Latensi Uji Terakhir</span>
                                <span class="small fw-bold text-success" id="statusLastLatency">
                                    <?= !empty($data['settings']['last_test_latency']) ? htmlspecialchars($data['settings']['last_test_latency']) . ' ms' : '-'; ?>
                                </span>
                            </li>
                        </ul>

                        <?php if (!empty($data['settings']['last_test_message'])): ?>
                            <div class="p-2 px-3 rounded-3 bg-light border small text-muted" id="statusLastTestMsg">
                                <i class="bi bi-chat-left-dots me-1 text-primary"></i>
                                <?= htmlspecialchars($data['settings']['last_test_message']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Card D: Pengaturan Chatbot & Persona -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-shape icon-shape-sm bg-gradient-info text-white rounded-3 d-flex align-items-center justify-content-center p-2">
                                <i class="bi bi-chat-square-quote text-white"></i>
                            </div>
                            <h5 class="fw-bold mb-0 text-dark">Persona & Perilaku Chatbot</h5>
                        </div>
                    </div>

                    <div class="card-body p-4 pt-2">
                        <!-- Nama Chatbot & Bahasa -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-7">
                                <label for="bot_name" class="form-label small fw-semibold text-dark">Nama Chatbot</label>
                                <input type="text" class="form-control" id="bot_name" name="bot_name" required maxlength="100" value="<?= htmlspecialchars($data['settings']['bot_name'] ?? 'ADCom Virtual Assistant'); ?>">
                            </div>
                            <div class="col-md-5">
                                <label for="response_language" class="form-label small fw-semibold text-dark">Bahasa Jawaban</label>
                                <select class="form-select" id="response_language" name="response_language">
                                    <option value="id" <?= (($data['settings']['response_language'] ?? 'id') === 'id') ? 'selected' : ''; ?>>Bahasa Indonesia</option>
                                    <option value="en" <?= (($data['settings']['response_language'] ?? 'id') === 'en') ? 'selected' : ''; ?>>English</option>
                                </select>
                            </div>
                        </div>

                        <!-- Bot Tone -->
                        <div class="mb-3">
                            <label for="bot_tone" class="form-label small fw-semibold text-dark">Gaya Komunikasi (Tone)</label>
                            <select class="form-select" id="bot_tone" name="bot_tone">
                                <option value="friendly_professional" <?= (($data['settings']['bot_tone'] ?? 'friendly_professional') === 'friendly_professional') ? 'selected' : ''; ?>>Ramah & Profesional (Direkomendasikan)</option>
                                <option value="formal" <?= (($data['settings']['bot_tone'] ?? '') === 'formal') ? 'selected' : ''; ?>>Formal & Baku</option>
                                <option value="concise" <?= (($data['settings']['bot_tone'] ?? '') === 'concise') ? 'selected' : ''; ?>>Ringkas & To-the-point</option>
                            </select>
                        </div>

                        <!-- Pesan Sambutan -->
                        <div class="mb-3">
                            <label for="welcome_message" class="form-label small fw-semibold text-dark">Pesan Sambutan Awal (Welcome Message)</label>
                            <textarea class="form-control" id="welcome_message" name="welcome_message" rows="3" required><?= htmlspecialchars($data['settings']['welcome_message'] ?? 'Halo! Saya Asisten Virtual ADCom Jasa. Ada yang bisa saya bantu seputar layanan servis komputer, laptop, printer, atau instalasi jaringan kami hari ini?'); ?></textarea>
                            <div class="form-text small" style="font-size: 11px;">Pesan yang pertama kali ditampilkan saat pengguna membuka kotak dialog chatbot.</div>
                        </div>

                        <!-- System Instruction / Persona Textarea -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="system_instruction" class="form-label small fw-semibold text-dark mb-0">System Instruction (Prompt Utama)</label>
                                <span class="badge bg-secondary-subtle text-secondary small" id="instructionCharCount">0 karakter</span>
                            </div>
                            <textarea class="form-control font-monospace small" id="system_instruction" name="system_instruction" rows="6" required style="font-size: 12px; line-height: 1.4;"><?= htmlspecialchars($data['settings']['system_instruction'] ?? "Anda adalah Chatbot Asisten AI untuk 'ADCom Jasa TKJ'. Tugas Anda adalah menjawab pertanyaan pengguna, menjelaskan paket layanan jasa, membantu keluhan customer, alur pemesanan, dan simulasi harga secara akurat dan ramah. Gunakan bahasa Indonesia yang baik dan sopan."); ?></textarea>
                            <div class="form-text small" style="font-size: 11px;">Instruksi dasar yang menentukan identitas, batasan domain, dan etika jawaban AI pada setiap prompt percakapan.</div>
                        </div>
                    </div>
                </div>

                <!-- Card E: Tombol Tindakan & Reset -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill py-2 fw-semibold">
                                <i class="bi bi-save2 me-1"></i>Simpan Konfigurasi
                            </button>
                            <button type="button" class="btn btn-outline-danger rounded-pill py-2 btn-sm" data-bs-toggle="modal" data-bs-target="#modalResetDefault">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>Reset ke Default
                            </button>
                            <a href="<?= BASEURL; ?>/aiConfig" class="btn btn-link text-muted btn-sm text-decoration-none text-center">
                                Batalkan Perubahan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal Konfirmasi Reset ke Default -->
<div class="modal fade" id="modalResetDefault" tabindex="-1" aria-labelledby="modalResetDefaultLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger d-flex align-items-center gap-2" id="modalResetDefaultLabel">
                    <i class="bi bi-exclamation-triangle-fill"></i>Konfirmasi Reset Konfigurasi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <p class="text-muted mb-3">
                    Apakah Anda yakin ingin mengembalikan seluruh parameter model, batasan token, temperature, pesan sambutan, dan system instruction AI ke pengaturan awal (default)?
                </p>
                <div class="alert alert-warning small border-0 rounded-3 mb-0">
                    <i class="bi bi-info-circle-fill me-1"></i>
                    <strong>Catatan:</strong> API Key Google Gemini yang tersimpan di file <code>.env</code> tidak akan terhapus.
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                <form action="<?= BASEURL; ?>/aiConfig/resetDefault" method="POST" class="d-inline">
                    <?= CSRF::input(); ?>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Ya, Reset ke Default
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Sinkronisasi Slider & Number Input Temperature
    const tempRange = document.getElementById('ai_temperature_range');
    const tempInput = document.getElementById('ai_temperature');
    const tempBadge = document.getElementById('tempValueBadge');

    function updateTemperature(val) {
        val = parseFloat(val).toFixed(1);
        if (isNaN(val)) val = '0.7';
        if (val < 0.0) val = '0.0';
        if (val > 2.0) val = '2.0';
        tempRange.value = val;
        tempInput.value = val;
        tempBadge.textContent = val;
    }

    tempRange.addEventListener('input', function () {
        updateTemperature(this.value);
    });
    tempInput.addEventListener('change', function () {
        updateTemperature(this.value);
    });

    // 2. Deskripsi Dinamis Pilihan Model
    const modelDescriptions = {
        'gemini-flash-latest': 'Generasi mutakhir Gemini Flash: respons super cepat, cerdas, dan efisien untuk interaksi pelanggan.',
        'gemini-2.5-flash': 'Cocok untuk FAQ, layanan pelanggan, dan konsultasi interaktif seputar pesanan servis.',
        'gemini-2.5-pro': 'Ideal untuk kalkulasi harga jasa kompleks, analisis kerusakan teknis mendalam, dan troubleshooting.',
        'gemini-pro-latest': 'Versi Pro terbaru dengan pemahaman konteks percakapan yang sangat tinggi.',
        'gemini-2.5-flash-lite': 'Varian ringan untuk respon cepat dengan penggunaan sumber daya minimal.'
    };

    const modelSelect = document.getElementById('ai_model');
    const modelDescText = document.getElementById('modelDescriptionText');

    modelSelect.addEventListener('change', function () {
        const val = this.value;
        modelDescText.textContent = modelDescriptions[val] || 'Model Google Gemini resmi.';
    });

    // 3. Karakter Counter System Instruction
    const systemInstruction = document.getElementById('system_instruction');
    const charCountBadge = document.getElementById('instructionCharCount');

    function updateCharCount() {
        charCountBadge.textContent = systemInstruction.value.length + ' karakter';
    }
    systemInstruction.addEventListener('input', updateCharCount);
    updateCharCount();

    // 4. Show / Hide API Key
    const apiKeyInput = document.getElementById('ai_api_key');
    const btnToggle = document.getElementById('btnToggleApiKey');
    const toggleIcon = document.getElementById('toggleIcon');

    btnToggle.addEventListener('click', function () {
        if (apiKeyInput.type === 'password') {
            apiKeyInput.type = 'text';
            toggleIcon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            apiKeyInput.type = 'password';
            toggleIcon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });

    // 5. Uji Koneksi Gemini Real-time via AJAX
    const btnTest = document.getElementById('btnTestConnection');
    const testIcon = document.getElementById('testBtnIcon');
    const testText = document.getElementById('testBtnText');
    const alertBox = document.getElementById('testResultAlert');
    const alertIcon = document.getElementById('testAlertIcon');
    const alertTitle = document.getElementById('testAlertTitle');
    const alertMsg = document.getElementById('testAlertMessage');
    const alertMeta = document.getElementById('testAlertMeta');

    btnTest.addEventListener('click', function () {
        // Tampilkan status loading pada tombol
        btnTest.disabled = true;
        testIcon.className = 'spinner-border spinner-border-sm me-1';
        testText.textContent = 'Menghubungi Google Gemini...';

        const csrfToken = document.querySelector('input[name="csrf_token"]').value;
        const model = modelSelect.value;
        const apiKey = apiKeyInput.value.trim();

        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        formData.append('ai_model', model);
        if (apiKey) {
            formData.append('ai_api_key', apiKey);
        }

        fetch('<?= BASEURL; ?>/aiConfig/testConnection', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            btnTest.disabled = false;
            testIcon.className = 'bi bi-broadcast me-1';
            testText.textContent = 'Uji Koneksi Gemini Sekarang';

            alertBox.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning');

            if (data.success) {
                alertBox.classList.add('alert-success');
                alertIcon.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i>';
                alertTitle.textContent = 'Uji Koneksi Berhasil!';
                alertMsg.textContent = data.message;
                alertMeta.textContent = 'Model: ' + (data.model || model) + ' | Latensi: ' + (data.latency || '-') + ' ms | Waktu: ' + new Date().toLocaleTimeString();

                // Perbarui status kartu realtime
                const statusLat = document.getElementById('statusLastLatency');
                if (statusLat) statusLat.textContent = (data.latency || '-') + ' ms';
                const statusTime = document.getElementById('statusLastTestAt');
                if (statusTime) statusTime.textContent = 'Baru saja';
                const statusMsg = document.getElementById('statusLastTestMsg');
                if (statusMsg) statusMsg.innerHTML = '<i class="bi bi-chat-left-dots me-1 text-primary"></i>' + data.message;
            } else {
                alertBox.classList.add('alert-danger');
                alertIcon.innerHTML = '<i class="bi bi-x-circle-fill text-danger"></i>';
                alertTitle.textContent = 'Uji Koneksi Gagal';
                alertMsg.textContent = data.message || 'Tidak dapat terhubung ke Google Gemini API.';
                alertMeta.textContent = 'Model: ' + (data.model || model) + (data.latency ? ' | Percobaan: ' + data.latency + ' ms' : '') + ' | Waktu: ' + new Date().toLocaleTimeString();
            }

            alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        })
        .catch(err => {
            btnTest.disabled = false;
            testIcon.className = 'bi bi-broadcast me-1';
            testText.textContent = 'Uji Koneksi Gemini Sekarang';

            alertBox.classList.remove('d-none', 'alert-success', 'alert-warning');
            alertBox.classList.add('alert-danger');
            alertIcon.innerHTML = '<i class="bi bi-exclamation-triangle-fill text-danger"></i>';
            alertTitle.textContent = 'Kesalahan Jaringan / Server';
            alertMsg.textContent = 'Tidak dapat menghubungi server lokal untuk memproses pengujian koneksi.';
            alertMeta.textContent = 'Error: ' + err.message;
        });
    });
});
</script>
