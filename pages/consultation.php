<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/booking_helpers.php';

requireLogin();

$pageTitle = 'Ruang Konsultasi - CareSync';
$currentPage = 'history';

$user = currentUser();
$userId = (int) ($user['id'] ?? 0);
$consultations = getUserConsultations($pdo, $userId);
$requestedConsultationId = (int) ($_GET['consultation_id'] ?? 0);
$selectedConsultation = null;

foreach ($consultations as $consultation) {
    if ($consultation['id'] === $requestedConsultationId) {
        $selectedConsultation = $consultation;
        break;
    }
}

if (!$selectedConsultation && !empty($consultations)) {
    $selectedConsultation = $consultations[0];
}

$initialMessages = [];
$initialLastMessageId = 0;

if ($selectedConsultation) {
    ensureConsultationIntroMessage($pdo, $selectedConsultation);
    $selectedConsultation = findAccessibleConsultation($pdo, $selectedConsultation['id'], $userId) ?? $selectedConsultation;
    $initialMessages = getConsultationMessages($pdo, $selectedConsultation['id']);

    foreach ($initialMessages as $message) {
        $initialLastMessageId = max($initialLastMessageId, (int) $message['id']);
    }
}

include __DIR__ . '/../includes/header.php';
?>

<style>
  .consult-shell {
    min-height: calc(100vh - var(--nav-h));
    background:
      radial-gradient(circle at top left, rgba(29, 78, 216, 0.08), transparent 28%),
      linear-gradient(180deg, #f8fbff 0%, #f8fafc 42%, #eef4ff 100%);
    padding: 28px 20px 32px;
  }

  .consult-frame {
    max-width: 1380px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 320px minmax(0, 1fr);
    gap: 22px;
    align-items: start;
  }

  .consult-panel {
    background: rgba(255, 255, 255, 0.94);
    border: 1px solid rgba(148, 163, 184, 0.18);
    border-radius: 28px;
    box-shadow: 0 18px 48px -28px rgba(15, 23, 42, 0.22);
    backdrop-filter: blur(12px);
  }

  .consult-sidebar {
    overflow: hidden;
    position: sticky;
    top: 92px;
  }

  .sidebar-top {
    padding: 22px;
    border-bottom: 1px solid rgba(226, 232, 240, 0.9);
  }

  .sidebar-caption {
    margin: 0 0 6px;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: .16em;
    text-transform: uppercase;
    color: #64748b;
  }

  .sidebar-title {
    margin: 0;
    font-size: 24px;
    font-weight: 800;
    color: #0f172a;
  }

  .sidebar-subtitle {
    margin: 10px 0 0;
    color: #64748b;
    font-size: 14px;
    line-height: 1.55;
  }

  .sidebar-search {
    position: relative;
    margin-top: 16px;
  }

  .sidebar-search input {
    width: 100%;
    border: 1px solid #dbe4f0;
    background: #f8fafc;
    border-radius: 18px;
    padding: 12px 14px 12px 40px;
    font: inherit;
    font-size: 14px;
    outline: none;
    transition: .2s ease;
  }

  .sidebar-search input:focus {
    border-color: #2563eb;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.08);
  }

  .sidebar-search i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
  }

  .consult-list {
    max-height: calc(100vh - 210px);
    overflow-y: auto;
    padding: 8px;
  }

  .consult-item {
    width: 100%;
    text-align: left;
    border: none;
    background: transparent;
    display: flex;
    gap: 14px;
    padding: 14px;
    border-radius: 22px;
    cursor: pointer;
    transition: .2s ease;
    font: inherit;
  }

  .consult-item:hover {
    background: #f8fafc;
  }

  .consult-item.active {
    background: linear-gradient(135deg, rgba(29, 78, 216, 0.12), rgba(59, 130, 246, 0.06));
    box-shadow: inset 0 0 0 1px rgba(37, 99, 235, 0.12);
  }

  .consult-avatar {
    width: 48px;
    height: 48px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: #fff;
    font-size: 14px;
    font-weight: 800;
    box-shadow: 0 10px 24px -16px rgba(15, 23, 42, 0.45);
  }

  .consult-item-body {
    min-width: 0;
    flex: 1;
  }

  .consult-item-row {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    align-items: start;
    margin-bottom: 3px;
  }

  .consult-item-name {
    margin: 0;
    color: #0f172a;
    font-size: 14px;
    font-weight: 800;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .consult-item-time {
    flex-shrink: 0;
    color: #94a3b8;
    font-size: 11px;
    font-weight: 700;
  }

  .consult-item-subtitle {
    margin: 0 0 6px;
    color: #2563eb;
    font-size: 12px;
    font-weight: 700;
  }

  .consult-item-preview {
    margin: 0;
    color: #64748b;
    font-size: 12px;
    line-height: 1.5;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .02em;
  }

  .status-menunggu {
    background: #fff7ed;
    color: #c2410c;
  }

  .status-berjalan {
    background: #ecfdf5;
    color: #047857;
  }

  .status-selesai {
    background: #eff6ff;
    color: #1d4ed8;
  }

  .chat-panel {
    overflow: hidden;
    display: grid;
    grid-template-rows: auto minmax(0, 1fr) auto;
    min-height: calc(100vh - 100px);
  }

  .chat-header {
    padding: 22px 24px;
    border-bottom: 1px solid rgba(226, 232, 240, 0.92);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    background: linear-gradient(180deg, rgba(248, 250, 252, 0.95), rgba(255, 255, 255, 0.95));
  }

  .chat-header-main {
    display: flex;
    align-items: center;
    gap: 16px;
    min-width: 0;
  }

  .chat-header-avatar {
    width: 58px;
    height: 58px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 18px;
    font-weight: 800;
    box-shadow: 0 22px 32px -26px rgba(15, 23, 42, 0.65);
  }

  .chat-header-copy {
    min-width: 0;
  }

  .chat-header-copy h1 {
    margin: 0;
    font-size: 22px;
    font-weight: 800;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .chat-header-copy p {
    margin: 6px 0 0;
    font-size: 14px;
    color: #64748b;
  }

  .chat-header-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: flex-end;
  }

  .chat-meta {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 11px 14px;
    border-radius: 18px;
    background: #f8fafc;
    color: #475569;
    font-size: 13px;
    font-weight: 700;
  }

  .chat-meta-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #10b981;
    box-shadow: 0 0 0 6px rgba(16, 185, 129, 0.12);
  }

  .chat-action-btn {
    border: none;
    border-radius: 16px;
    padding: 12px 16px;
    font: inherit;
    font-weight: 800;
    cursor: pointer;
    transition: .2s ease;
  }

  .chat-action-btn.primary {
    background: linear-gradient(135deg, #1d4ed8, #2563eb);
    color: #fff;
    box-shadow: 0 18px 24px -24px rgba(29, 78, 216, 0.95);
  }

  .chat-action-btn.ghost {
    background: #eff6ff;
    color: #1d4ed8;
  }

  .chat-action-btn:hover {
    transform: translateY(-1px);
  }

  .chat-action-btn:disabled {
    cursor: not-allowed;
    opacity: .55;
    transform: none;
    box-shadow: none;
  }

  .chat-messages {
    padding: 24px;
    overflow-y: auto;
    background:
      linear-gradient(180deg, rgba(248, 250, 252, 0.58), rgba(255, 255, 255, 0.96)),
      radial-gradient(circle at top right, rgba(29, 78, 216, 0.05), transparent 30%);
    display: flex;
    flex-direction: column;
    gap: 14px;
  }

  .chat-day-banner {
    align-self: center;
    background: rgba(226, 232, 240, 0.92);
    color: #475569;
    padding: 7px 14px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
  }

  .msg-row {
    display: flex;
    gap: 10px;
    align-items: flex-end;
    max-width: min(74%, 720px);
  }

  .msg-row.mine {
    align-self: flex-end;
    flex-direction: row-reverse;
  }

  .msg-mini-avatar {
    width: 34px;
    height: 34px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 11px;
    font-weight: 800;
    flex-shrink: 0;
  }

  .msg-card {
    min-width: 0;
  }

  .msg-bubble {
    padding: 12px 14px;
    border-radius: 18px;
    font-size: 14px;
    line-height: 1.6;
    word-break: break-word;
    white-space: pre-wrap;
    box-shadow: 0 14px 24px -22px rgba(15, 23, 42, 0.45);
  }

  .msg-bubble.theirs {
    background: #fff;
    color: #0f172a;
    border-bottom-left-radius: 6px;
  }

  .msg-bubble.mine {
    background: linear-gradient(135deg, #1d4ed8, #2563eb);
    color: #fff;
    border-bottom-right-radius: 6px;
  }

  .msg-bubble a {
    color: inherit;
    font-weight: 800;
    text-decoration: underline;
  }

  .msg-time {
    margin-top: 5px;
    color: #94a3b8;
    font-size: 11px;
    font-weight: 700;
  }

  .chat-input-area {
    padding: 18px 20px 20px;
    border-top: 1px solid rgba(226, 232, 240, 0.92);
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 14px;
    align-items: end;
    background: rgba(255, 255, 255, 0.94);
  }

  .chat-input-box {
    border: 1px solid #dbe4f0;
    background: #f8fafc;
    border-radius: 22px;
    padding: 6px;
    display: flex;
    gap: 8px;
    align-items: end;
  }

  .chat-textarea {
    width: 100%;
    min-height: 54px;
    max-height: 140px;
    border: none;
    background: transparent;
    resize: none;
    outline: none;
    padding: 12px 14px;
    font: inherit;
    font-size: 14px;
    line-height: 1.55;
    color: #0f172a;
  }

  .chat-send-btn {
    width: 54px;
    height: 54px;
    border: none;
    border-radius: 18px;
    background: linear-gradient(135deg, #1d4ed8, #2563eb);
    color: #fff;
    cursor: pointer;
    transition: .2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 18px 24px -24px rgba(29, 78, 216, 0.95);
  }

  .chat-send-btn:hover {
    transform: translateY(-1px);
  }

  .chat-send-btn:disabled {
    cursor: not-allowed;
    opacity: .55;
    transform: none;
  }

  .chat-input-note {
    color: #94a3b8;
    font-size: 12px;
    font-weight: 700;
    padding-left: 6px;
  }

  .chat-empty {
    align-self: center;
    text-align: center;
    max-width: 420px;
    padding: 28px;
    color: #64748b;
  }

  .chat-empty i {
    font-size: 34px;
    color: #2563eb;
    margin-bottom: 12px;
  }

  .empty-state {
    max-width: 740px;
    margin: 60px auto 0;
    padding: 42px;
    text-align: center;
  }

  .empty-state i {
    width: 84px;
    height: 84px;
    border-radius: 28px;
    background: #eff6ff;
    color: #1d4ed8;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    margin-bottom: 18px;
  }

  .empty-state h2 {
    margin: 0;
    color: #0f172a;
    font-size: 28px;
    font-weight: 800;
  }

  .empty-state p {
    max-width: 520px;
    margin: 14px auto 26px;
    color: #64748b;
    line-height: 1.7;
  }

  .empty-state a {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 22px;
    border-radius: 18px;
    background: linear-gradient(135deg, #1d4ed8, #2563eb);
    color: #fff;
    text-decoration: none;
    font-weight: 800;
  }

  .toast-stack {
    position: fixed;
    right: 22px;
    bottom: 22px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .toast-card {
    min-width: 270px;
    max-width: 360px;
    padding: 14px 16px;
    border-radius: 18px;
    background: rgba(15, 23, 42, 0.96);
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    box-shadow: 0 20px 36px -24px rgba(15, 23, 42, 0.7);
  }

  .video-overlay {
    position: fixed;
    inset: 0;
    background: rgba(2, 6, 23, 0.95);
    z-index: 1200;
    display: none;
    flex-direction: column;
  }

  .video-overlay.active {
    display: flex;
  }

  .video-topbar {
    padding: 16px 22px;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  }

  .video-surface {
    flex: 1;
    position: relative;
    overflow: hidden;
    background:
      radial-gradient(circle at top, rgba(37, 99, 235, 0.22), transparent 36%),
      linear-gradient(180deg, #111827, #020617);
  }

  .video-card {
    text-align: center;
    color: rgba(255, 255, 255, 0.92);
    max-width: 520px;
    padding: 24px;
  }

  .video-avatar {
    width: 96px;
    height: 96px;
    border-radius: 32px;
    margin: 0 auto 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    font-weight: 800;
    color: #fff;
    box-shadow: 0 24px 48px -28px rgba(37, 99, 235, 0.85);
  }

  .video-meeting-shell {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
  }

  .video-placeholder {
    position: absolute;
    inset: 0;
    display: grid;
    place-items: center;
    padding: 24px;
    z-index: 1;
  }

  .video-placeholder.hidden {
    display: none;
  }

  .video-room-meta {
    margin-top: 14px;
    font-size: 13px;
    color: rgba(255, 255, 255, 0.72);
    word-break: break-word;
  }

  .video-meeting {
    position: absolute;
    inset: 0;
    z-index: 2;
  }

  .video-controls {
    padding: 18px 22px 24px;
    display: flex;
    justify-content: center;
    gap: 12px;
  }

  .video-btn {
    width: 54px;
    height: 54px;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    color: #fff;
    background: rgba(255, 255, 255, 0.16);
    transition: .2s ease;
  }

  .video-btn.end {
    background: #dc2626;
  }

  .video-btn:hover {
    transform: scale(1.03);
  }

  @media (max-width: 1080px) {
    .consult-frame {
      grid-template-columns: 1fr;
    }

    .consult-sidebar {
      position: static;
    }

    .consult-list {
      max-height: 320px;
    }
  }

  @media (max-width: 760px) {
    .consult-shell {
      padding: 18px 12px 24px;
    }

    .chat-panel {
      min-height: calc(100vh - 130px);
    }

    .chat-header {
      padding: 18px;
      flex-direction: column;
      align-items: stretch;
    }

    .chat-header-actions {
      justify-content: stretch;
    }

    .chat-meta,
    .chat-action-btn {
      width: 100%;
      justify-content: center;
    }

    .chat-input-area {
      grid-template-columns: 1fr;
    }

    .chat-send-btn {
      width: 100%;
      border-radius: 18px;
    }

    .msg-row {
      max-width: 100%;
    }
  }
</style>

<div class="consult-shell">
  <?php if (!$selectedConsultation): ?>
    <div class="consult-panel empty-state">
      <i class="fa-solid fa-comments"></i>
      <h2>Belum ada sesi konsultasi</h2>
      <p>Silakan booking dokter terlebih dahulu agar pasien bisa memilih spesialisasi, mengambil slot yang tersedia, lalu masuk ke ruang chat konsultasi yang sesuai.</p>
      <a href="<?= BASE_URL ?>/pages/booking.php">
        <i class="fa-solid fa-calendar-plus"></i>
        Booking Konsultasi
      </a>
    </div>
  <?php else: ?>
    <div class="consult-frame">
      <aside class="consult-panel consult-sidebar">
        <div class="sidebar-top">
          <p class="sidebar-caption">Ruang Konsultasi</p>
          <div class="sidebar-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="consult-search" placeholder="Cari dokter atau spesialisasi...">
          </div>
        </div>

        <div class="consult-list" id="consult-list">
          <?php foreach ($consultations as $consultation): ?>
            <?php
              $statusClass = strtolower($consultation['status']);
              $isActive = $consultation['id'] === $selectedConsultation['id'];
              $searchIndex = strtolower($consultation['display_name'] . ' ' . $consultation['display_subtitle'] . ' ' . $consultation['specialization']);
            ?>
            <button
              type="button"
              class="consult-item<?= $isActive ? ' active' : '' ?>"
              data-session-id="<?= $consultation['id'] ?>"
              data-search="<?= htmlspecialchars($searchIndex) ?>"
            >
              <div class="consult-avatar" style="background: <?= htmlspecialchars($consultation['avatar_color']) ?>;">
                <?= htmlspecialchars($consultation['initials']) ?>
              </div>
              <div class="consult-item-body">
                <div class="consult-item-row">
                  <p class="consult-item-name"><?= htmlspecialchars($consultation['display_name']) ?></p>
                  <span class="consult-item-time"><?= htmlspecialchars($consultation['last_message_label']) ?></span>
                </div>
                <p class="consult-item-subtitle"><?= htmlspecialchars($consultation['display_subtitle']) ?></p>
                <p class="consult-item-preview" id="preview-<?= $consultation['id'] ?>"><?= htmlspecialchars($consultation['preview']) ?></p>
                <span class="status-badge status-<?= $statusClass ?>" id="badge-<?= $consultation['id'] ?>">
                  <i class="fa-solid fa-circle"></i>
                  <?= htmlspecialchars($consultation['status']) ?>
                </span>
              </div>
            </button>
          <?php endforeach; ?>
        </div>
      </aside>

      <section class="consult-panel chat-panel">
        <div class="chat-header">
          <div class="chat-header-main">
            <div class="chat-header-avatar" id="chat-avatar" style="background: <?= htmlspecialchars($selectedConsultation['avatar_color']) ?>;">
              <?= htmlspecialchars($selectedConsultation['initials']) ?>
            </div>
            <div class="chat-header-copy">
              <h1 id="chat-title"><?= htmlspecialchars($selectedConsultation['display_name']) ?></h1>
              <p id="chat-subtitle"><?= htmlspecialchars($selectedConsultation['display_subtitle']) ?> � Jadwal <?= htmlspecialchars($selectedConsultation['scheduled_label']) ?></p>
            </div>
          </div>

          <div class="chat-header-actions">
            <div class="chat-meta">
              <span class="chat-meta-dot"></span>
              <span id="chat-status"><?= htmlspecialchars($selectedConsultation['status']) ?></span>
            </div>
            <button type="button" class="chat-action-btn primary" id="start-video">
              <i class="fa-solid fa-video"></i>
              Video Call
            </button>
          </div>
        </div>

        <div class="chat-messages" id="chat-messages">
          <div class="chat-day-banner" id="chat-day-banner">
            Sesi dijadwalkan � <?= htmlspecialchars($selectedConsultation['scheduled_label']) ?>
          </div>
          <div id="chat-retention-note" class="mx-5 mt-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-700">
            <?= htmlspecialchars($selectedConsultation['chat_retention_notice'] ?? 'Riwayat chat tersedia selama konsultasi berlangsung.') ?>
          </div>

          <?php if (!$initialMessages): ?>
            <div class="chat-empty" id="chat-empty-state">
              <i class="fa-solid fa-comment-medical"></i>
              <div><?= htmlspecialchars(!empty($selectedConsultation['chat_expired']) ? ($selectedConsultation['chat_retention_notice'] ?? 'Riwayat chat konsultasi sudah dihapus.') : 'Belum ada pesan. Mulai percakapan untuk konsultasi dengan dokter.') ?></div>
            </div>
          <?php else: ?>
            <?php foreach ($initialMessages as $message): ?>
              <?php $isMine = (int) $message['sender_id'] === $userId; ?>
              <div class="msg-row<?= $isMine ? ' mine' : '' ?>" data-message-id="<?= (int) $message['id'] ?>">
                <?php if (!$isMine): ?>
                  <div class="msg-mini-avatar" style="background: <?= htmlspecialchars($selectedConsultation['avatar_color']) ?>;">
                    <?= htmlspecialchars($selectedConsultation['initials']) ?>
                  </div>
                <?php endif; ?>
              <div class="msg-card">
                  <div class="msg-bubble <?= $isMine ? 'mine' : 'theirs' ?>"><?= preg_replace(
                      '~(https?://[^\s<]+)~i',
                      '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>',
                      nl2br(htmlspecialchars($message['body']))
                  ) ?></div>
                  <div class="msg-time"<?= $isMine ? ' style="text-align:right"' : '' ?>><?= htmlspecialchars($message['time']) ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <div class="chat-input-area">
          <div>
            <div class="chat-input-box">
              <textarea
                id="msg-input"
                class="chat-textarea"
                rows="1"
                placeholder="Tulis pesan untuk dokter..."
              ></textarea>
            </div>
            <div class="chat-input-note">Tekan `Enter` untuk kirim, `Shift + Enter` untuk baris baru.</div>
          </div>
          <button type="button" class="chat-send-btn" id="send-message" aria-label="Kirim pesan">
            <i class="fa-solid fa-paper-plane"></i>
          </button>
        </div>

        <div id="review-panel" class="hidden border-t border-slate-100 bg-amber-50/50 px-5 py-4">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
              <div class="text-sm font-extrabold text-slate-900">Review Konsultasi</div>
              <div class="text-xs text-slate-500 mt-1">Setelah diagnosis dokter terbit, Anda bisa memberi rating bintang untuk sesi ini.</div>
            </div>
            <div id="review-stars" class="flex items-center gap-2"></div>
          </div>
        </div>
      </section>
    </div>
  <?php endif; ?>
</div>

<div class="toast-stack" id="toast-stack"></div>

<div class="video-overlay" id="video-overlay">
  <div class="video-topbar">
    <div style="display:flex;align-items:center;gap:14px">
      <div class="video-avatar" id="video-avatar" style="background: <?= htmlspecialchars($selectedConsultation['avatar_color'] ?? '#1D4ED8') ?>;width:52px;height:52px;border-radius:18px;font-size:18px;margin:0;">
        <?= htmlspecialchars($selectedConsultation['initials'] ?? 'DR') ?>
      </div>
      <div>
        <div id="video-title" style="font-size:16px;font-weight:800"><?= htmlspecialchars($selectedConsultation['display_name'] ?? 'Dokter CareSync') ?></div>
        <div id="video-subtitle" style="font-size:13px;color:rgba(255,255,255,.7)">Menghubungkan ke sesi konsultasi...</div>
      </div>
    </div>
    <div id="video-timer" style="font-size:14px;font-weight:800;color:rgba(255,255,255,.88);display:none">00:00</div>
  </div>

  <div class="video-surface">
    <div class="video-meeting-shell">
      <div class="video-placeholder" id="video-placeholder">
        <div class="video-card">
          <div class="video-avatar" id="video-main-avatar" style="background: <?= htmlspecialchars($selectedConsultation['avatar_color'] ?? '#1D4ED8') ?>;">
            <?= htmlspecialchars($selectedConsultation['initials'] ?? 'DR') ?>
          </div>
          <div id="video-main-title" style="font-size:22px;font-weight:800"><?= htmlspecialchars($selectedConsultation['display_name'] ?? 'Dokter CareSync') ?></div>
          <div id="video-main-copy" style="margin-top:10px;color:rgba(255,255,255,.72)">Menyiapkan ruang Jitsi Meet untuk sesi konsultasi ini.</div>
          <div class="video-room-meta" id="video-room-meta"></div>
        </div>
      </div>
      <div class="video-meeting" id="jitsi-container"></div>
    </div>
  </div>

  <div class="video-controls">
    <button type="button" class="video-btn" id="open-video-external" title="Buka di tab baru">
      <i class="fa-solid fa-up-right-from-square"></i>
    </button>
    <button type="button" class="video-btn end" id="end-video" title="Akhiri">
      <i class="fa-solid fa-phone-slash"></i>
    </button>
  </div>
</div>

<?php if ($selectedConsultation): ?>
<script>
const BASE_URL = '<?= BASE_URL ?>';
const JITSI_BASE_URL = '<?= htmlspecialchars(JITSI_BASE_URL, ENT_QUOTES) ?>';
const MY_USER_ID = <?= $userId ?>;
const consultations = <?= json_encode($consultations, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
const consultationMap = Object.fromEntries(consultations.map((item) => [item.id, item]));

let currentSessionId = <?= (int) $selectedConsultation['id'] ?>;
let lastMessageId = <?= $initialLastMessageId ?>;
let pollTimer = null;
let jitsiApi = null;
let jitsiScriptPromise = null;
let activeVideoSessionId = null;

function escapeHtml(text) {
  return String(text ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function nl2brSafe(text) {
  return escapeHtml(text).replace(/\n/g, '<br>');
}

function linkifySafe(text) {
  return nl2brSafe(text).replace(
    /(https?:\/\/[^\s<]+)/gi,
    '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>'
  );
}

function statusClass(status) {
  const normalized = String(status || '').toLowerCase();
  if (normalized === 'berjalan') return 'status-berjalan';
  if (normalized === 'selesai') return 'status-selesai';
  return 'status-menunggu';
}

function showToast(message) {
  const stack = document.getElementById('toast-stack');
  const toast = document.createElement('div');
  toast.className = 'toast-card';
  toast.textContent = message;
  stack.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(8px)';
    toast.style.transition = '.2s ease';
    setTimeout(() => toast.remove(), 220);
  }, 2600);
}

function scrollMessagesToBottom() {
  const container = document.getElementById('chat-messages');
  container.scrollTop = container.scrollHeight;
}

function updateSidebarSession(session) {
  const preview = document.getElementById(`preview-${session.id}`);
  const badge = document.getElementById(`badge-${session.id}`);

  if (preview) {
    preview.textContent = session.preview || 'Belum ada pesan.';
  }

  if (badge) {
    badge.className = `status-badge ${statusClass(session.status)}`;
    badge.innerHTML = `<i class="fa-solid fa-circle"></i> ${escapeHtml(session.status)}`;
  }
}

function updateHeader(session) {
  document.getElementById('chat-avatar').textContent = session.initials;
  document.getElementById('chat-avatar').style.background = session.avatar_color;
  document.getElementById('chat-title').textContent = session.display_name;
  document.getElementById('chat-subtitle').textContent = `${session.display_subtitle} � Jadwal ${session.scheduled_label}`;
  document.getElementById('chat-status').textContent = session.status;
  document.getElementById('chat-day-banner').textContent = `Sesi dijadwalkan � ${session.scheduled_label}`;
  const retentionNote = document.getElementById('chat-retention-note');
  if (retentionNote) {
    retentionNote.textContent = session.chat_retention_notice || 'Riwayat chat tersedia selama konsultasi berlangsung.';
  }
  updateReviewPanel(session);
  updateComposerState(session);
  updateSessionTimer(session);
}

function updateVideoIdentity(session) {
  const avatarIds = ['video-avatar', 'video-main-avatar'];
  avatarIds.forEach((id) => {
    const node = document.getElementById(id);
    node.textContent = session.initials;
    node.style.background = session.avatar_color;
  });
  document.getElementById('video-title').textContent = session.display_name;
  document.getElementById('video-main-title').textContent = session.display_name;
  document.getElementById('video-room-meta').textContent = session.video_call_room
    ? `Room: ${session.video_call_room}`
    : 'Room video call belum tersedia.';
}

function canJoinVideoCall(session) {
  const status = String(session?.status || '').toLowerCase();
  return status === 'berjalan' && Boolean(String(session?.video_call_room || '').trim());
}

function updateVideoButtonState(session) {
  const button = document.getElementById('start-video');
  const joinable = canJoinVideoCall(session);

  button.disabled = !joinable;
  button.title = joinable
    ? 'Gabung ke video call yang sudah dimulai dokter'
    : 'Video call baru bisa diikuti setelah dokter memulai sesi konsultasi.';
}

function renderStars(value, interactive = false) {
  let html = '';
  for (let star = 1; star <= 5; star += 1) {
    const filled = star <= value;
    const classes = filled ? 'text-amber-400' : 'text-slate-300';
    if (interactive) {
      html += `<button type="button" data-rating="${star}" class="review-star cursor-pointer text-2xl ${classes} hover:text-amber-400 transition">${filled ? '?' : '?'}</button>`;
    } else {
      html += `<span class="text-2xl ${classes}">${filled ? '?' : '?'}</span>`;
    }
  }
  return html;
}

function updateReviewPanel(session) {
  const panel = document.getElementById('review-panel');
  const stars = document.getElementById('review-stars');

  if (!panel || !stars) {
    return;
  }

  const canReview = Boolean(session?.can_review);
  const existingRating = Number(session?.patient_rating || 0);

  if (!canReview && existingRating <= 0) {
    panel.classList.add('hidden');
    stars.innerHTML = '';
    return;
  }

  panel.classList.remove('hidden');

  if (existingRating > 0) {
    stars.innerHTML = `<div class="flex items-center gap-2">${renderStars(existingRating)}<span class="text-sm font-bold text-slate-700 ml-2">${existingRating}/5</span></div>`;
    return;
  }

  stars.innerHTML = `<div class="flex items-center gap-2">${renderStars(0, true)}</div>`;
  stars.querySelectorAll('.review-star').forEach((button) => {
    button.addEventListener('click', () => submitReview(Number(button.dataset.rating || 0)));
  });
}

let sessionTimerInterval = null;

function formatTimeOnly(dateString) {
  if (!dateString) {
    return '';
  }

  const date = new Date(dateString.replace(' ', 'T'));
  if (Number.isNaN(date.getTime())) {
    return '';
  }

  return new Intl.DateTimeFormat('id-ID', {
    hour: '2-digit',
    minute: '2-digit',
    hour12: false,
    timeZone: 'Asia/Jakarta'
  }).format(date);
}

function formatDurationLabel(totalSeconds) {
  const safeSeconds = Math.max(0, Number(totalSeconds || 0));
  const hours = Math.floor(safeSeconds / 3600);
  const minutes = Math.floor((safeSeconds % 3600) / 60);
  const seconds = safeSeconds % 60;

  if (hours > 0) {
    return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
  }

  return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
}

function updateComposerState(session) {
  const input = document.getElementById('msg-input');
  const sendButton = document.getElementById('send-message');
  const closed = String(session?.status || '').toLowerCase() === 'selesai';
  const expired = Boolean(session?.chat_expired);

  if (!input || !sendButton) {
    return;
  }

  input.disabled = closed || expired;
  sendButton.disabled = closed || expired;
  input.placeholder = expired
    ? 'Riwayat chat sudah dihapus setelah 24 jam.'
    : (closed
      ? 'Sesi konsultasi sudah selesai.'
      : 'Tulis pesan untuk dokter...');
}

function updateSessionTimer(session) {
  const timerEl = document.getElementById('video-timer');
  const subtitleEl = document.getElementById('video-subtitle');
  const startedAt = String(session?.video_call_started_at || '').trim();
  const endedAt = String(session?.video_call_ended_at || '').trim();

  if (!timerEl || !subtitleEl) {
    return;
  }

  if (sessionTimerInterval) {
    clearInterval(sessionTimerInterval);
    sessionTimerInterval = null;
  }

  if (!startedAt) {
    timerEl.style.display = 'none';
    timerEl.textContent = '00:00';
    return;
  }

  const startedDate = new Date(startedAt.replace(' ', 'T'));
  const endedDate = endedAt ? new Date(endedAt.replace(' ', 'T')) : null;
  if (Number.isNaN(startedDate.getTime())) {
    timerEl.style.display = 'none';
    return;
  }

  subtitleEl.textContent = `Sesi konsultasi dimulai (${formatTimeOnly(startedAt)} WIB)`;
  timerEl.style.display = 'inline';

  const paint = () => {
    const targetDate = endedDate && !Number.isNaN(endedDate.getTime()) ? endedDate : new Date();
    const diffSeconds = Math.floor((targetDate.getTime() - startedDate.getTime()) / 1000);
    timerEl.textContent = formatDurationLabel(diffSeconds);
  };

  paint();

  if (!endedDate || Number.isNaN(endedDate.getTime())) {
    sessionTimerInterval = setInterval(paint, 1000);
  }
}

function renderEmptyState() {
  const container = document.getElementById('chat-messages');
  const session = consultationMap[currentSessionId] || {};
  const empty = document.createElement('div');
  empty.className = 'chat-empty';
  empty.id = 'chat-empty-state';
  empty.innerHTML = `
    <i class="fa-solid fa-comment-medical"></i>
    <div>${escapeHtml(session.chat_expired ? (session.chat_retention_notice || 'Riwayat chat konsultasi sudah dihapus.') : 'Belum ada pesan. Mulai percakapan untuk konsultasi dengan dokter.')}</div>
  `;
  container.appendChild(empty);
}

function removeEmptyState() {
  const empty = document.getElementById('chat-empty-state');
  if (empty) {
    empty.remove();
  }
}

function buildMessageRow(message, session) {
  const isMine = Number(message.sender_id) === Number(MY_USER_ID);
  const row = document.createElement('div');
  row.className = `msg-row${isMine ? ' mine' : ''}`;
  row.dataset.messageId = message.id;
  row.innerHTML = `
    ${isMine ? '' : `<div class="msg-mini-avatar" style="background:${session.avatar_color}">${escapeHtml(session.initials)}</div>`}
      <div class="msg-card">
      <div class="msg-bubble ${isMine ? 'mine' : 'theirs'}">${linkifySafe(message.body)}</div>
      <div class="msg-time"${isMine ? ' style="text-align:right"' : ''}>${escapeHtml(message.time)}</div>
    </div>
  `;
  return row;
}

function appendMessages(messages, replace = false) {
  const container = document.getElementById('chat-messages');
  const session = consultationMap[currentSessionId];

  if (replace) {
    container.innerHTML = `<div class="chat-day-banner" id="chat-day-banner">Sesi dijadwalkan � ${escapeHtml(session.scheduled_label)}</div>`;
    updateHeader(session);
  }

  if (!messages.length && replace) {
    renderEmptyState();
    return;
  }

  messages.forEach((message) => {
    if (container.querySelector(`[data-message-id="${message.id}"]`)) {
      return;
    }

    removeEmptyState();
    container.appendChild(buildMessageRow(message, session));
    lastMessageId = Math.max(lastMessageId, Number(message.id));
    session.preview = message.body;
    session.last_message_label = message.time;
  });

  updateSidebarSession(session);
  scrollMessagesToBottom();
}

async function fetchMessages(reset = false) {
  const sessionId = currentSessionId;
  const since = reset ? 0 : lastMessageId;

  try {
    const response = await fetch(`${BASE_URL}/api/chat/get.php?id_konsultasi=${sessionId}&last_id=${since}`, {
      credentials: 'same-origin'
    });
    const result = await response.json();

    if (!response.ok || result.status !== 'success') {
      throw new Error(result.message || 'Gagal memuat chat konsultasi.');
    }

    if (sessionId !== currentSessionId) {
      return;
    }

    if (result.data.consultation) {
      consultationMap[sessionId] = {
        ...consultationMap[sessionId],
        ...result.data.consultation
      };
      updateHeader(consultationMap[sessionId]);
      updateSidebarSession(consultationMap[sessionId]);
      updateVideoIdentity(consultationMap[sessionId]);
      updateVideoButtonState(consultationMap[sessionId]);
    }

    if (reset) {
      lastMessageId = 0;
      appendMessages(result.data.messages || [], true);
      return;
    }

    appendMessages(result.data.messages || []);
  } catch (error) {
    console.error(error);
  }
}

async function submitReview(rating) {
  if (!rating || !consultationMap[currentSessionId]?.can_review) {
    return;
  }

  try {
    const response = await fetch(`${BASE_URL}/api/consultation/review.php`, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        consultation_id: currentSessionId,
        rating
      })
    });
    const result = await response.json();

    if (!response.ok || result.status !== 'success') {
      throw new Error(result.message || 'Review gagal disimpan.');
    }

    if (result.data?.consultation) {
      consultationMap[currentSessionId] = {
        ...consultationMap[currentSessionId],
        ...result.data.consultation
      };
      updateHeader(consultationMap[currentSessionId]);
      updateSidebarSession(consultationMap[currentSessionId]);
    }

    showToast('Review bintang berhasil dikirim.');
  } catch (error) {
    showToast(error.message || 'Review gagal disimpan.');
  }
}

function setActiveSessionButton() {
  document.querySelectorAll('.consult-item').forEach((button) => {
    button.classList.toggle('active', Number(button.dataset.sessionId) === Number(currentSessionId));
  });
}

function openSession(sessionId) {
  if (!consultationMap[sessionId]) {
    return;
  }

  if (document.getElementById('video-overlay').classList.contains('active')) {
    endVideoCall(false);
  }

  currentSessionId = Number(sessionId);
  lastMessageId = 0;
  setActiveSessionButton();
  updateHeader(consultationMap[currentSessionId]);
  updateVideoIdentity(consultationMap[currentSessionId]);
  updateVideoButtonState(consultationMap[currentSessionId]);
  window.history.replaceState({}, '', `${BASE_URL}/pages/consultation.php?consultation_id=${currentSessionId}`);
  fetchMessages(true);
}

async function sendMessage() {
  const input = document.getElementById('msg-input');
  const button = document.getElementById('send-message');
  const text = input.value.trim();

  if (!text) {
    return;
  }

  button.disabled = true;

  try {
    const response = await fetch(`${BASE_URL}/api/chat/send.php`, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        idKonsultasi: currentSessionId,
        isiPesan: text
      })
    });
    const result = await response.json();

    if (!response.ok || result.status !== 'success') {
      throw new Error(result.message || 'Pesan gagal dikirim.');
    }

    appendMessages([result.data]);
    input.value = '';
    input.style.height = 'auto';
  } catch (error) {
    showToast(error.message || 'Pesan gagal dikirim.');
  } finally {
    button.disabled = false;
  }
}

function handleChatKey(event) {
  if (event.key === 'Enter' && !event.shiftKey) {
    event.preventDefault();
    sendMessage();
  }
}

function autoResize(element) {
  element.style.height = 'auto';
  element.style.height = `${Math.min(element.scrollHeight, 140)}px`;
}

function startPolling() {
  if (pollTimer) {
    clearInterval(pollTimer);
  }

  pollTimer = setInterval(() => fetchMessages(false), 2500);
}

function getJitsiDomain() {
  try {
    return new URL(JITSI_BASE_URL).hostname;
  } catch (error) {
    return 'meet.jit.si';
  }
}

function setVideoPlaceholder(message, show = true) {
  const placeholder = document.getElementById('video-placeholder');
  document.getElementById('video-main-copy').textContent = message;
  placeholder.classList.toggle('hidden', !show);
}

function loadJitsiScript() {
  if (window.JitsiMeetExternalAPI) {
    return Promise.resolve();
  }

  if (jitsiScriptPromise) {
    return jitsiScriptPromise;
  }

  jitsiScriptPromise = new Promise((resolve, reject) => {
    const script = document.createElement('script');
    script.src = `${JITSI_BASE_URL}/external_api.js`;
    script.async = true;
    script.onload = () => resolve();
    script.onerror = () => reject(new Error('Gagal memuat library Jitsi Meet.'));
    document.head.appendChild(script);
  });

  return jitsiScriptPromise;
}

function disposeJitsiApi() {
  if (jitsiApi) {
    jitsiApi.dispose();
    jitsiApi = null;
  }

  const container = document.getElementById('jitsi-container');
  container.innerHTML = '';
}

async function syncVideoSessionState(action = 'start') {
  const response = await fetch(`${BASE_URL}/api/consultation/video-session.php`, {
    method: 'POST',
    credentials: 'same-origin',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      idKonsultasi: currentSessionId,
      action
    })
  });
  const result = await response.json();

  if (!response.ok || result.status !== 'success') {
    throw new Error(result.message || 'Status video call gagal diperbarui.');
  }

  if (result.data?.consultation) {
    consultationMap[currentSessionId] = {
      ...consultationMap[currentSessionId],
      ...result.data.consultation
    };
    updateHeader(consultationMap[currentSessionId]);
    updateSidebarSession(consultationMap[currentSessionId]);
    updateVideoIdentity(consultationMap[currentSessionId]);
    updateVideoButtonState(consultationMap[currentSessionId]);
  }
}

async function startVideoCall() {
  const session = consultationMap[currentSessionId];
  const roomUrl = String(session?.video_call_url || '').trim();
  const roomName = String(session?.video_call_room || '').trim();
  const displayName = String('<?= htmlspecialchars($user['name'] ?? $user['nama'] ?? 'CareSync User', ENT_QUOTES) ?>');
  const email = String('<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES) ?>');
  const overlay = document.getElementById('video-overlay');

  if (!canJoinVideoCall(session)) {
    showToast('Tunggu dokter memulai video call terlebih dahulu.');
    return;
  }

  if (!roomUrl || !roomName) {
    showToast('Room video call belum tersedia untuk sesi ini.');
    return;
  }

  overlay.classList.add('active');
  document.body.style.overflow = 'hidden';
  activeVideoSessionId = currentSessionId;
  updateVideoIdentity(session);
  document.getElementById('video-subtitle').textContent = 'Menghubungkan ke sesi konsultasi...';
  document.getElementById('video-timer').style.display = 'inline';
  document.getElementById('video-timer').textContent = String(session.status || 'Berjalan');
  document.getElementById('open-video-external').dataset.url = roomUrl;
  setVideoPlaceholder('Menyiapkan ruang Jitsi Meet untuk sesi konsultasi ini.', true);
  disposeJitsiApi();

  try {
    await loadJitsiScript();

    jitsiApi = new window.JitsiMeetExternalAPI(getJitsiDomain(), {
      roomName,
      parentNode: document.getElementById('jitsi-container'),
      width: '100%',
      height: '100%',
      userInfo: {
        displayName,
        email
      },
      configOverwrite: {
        prejoinPageEnabled: false,
        startWithAudioMuted: false,
        startWithVideoMuted: false
      },
      interfaceConfigOverwrite: {
        DEFAULT_REMOTE_DISPLAY_NAME: 'Peserta Konsultasi',
        DISABLE_JOIN_LEAVE_NOTIFICATIONS: true
      }
    });

    jitsiApi.addListener('videoConferenceJoined', () => {
      document.getElementById('video-subtitle').textContent = 'Terhubung di halaman konsultasi';
      document.getElementById('video-timer').style.display = 'inline';
      document.getElementById('video-timer').textContent = 'Sedang video call';
      setVideoPlaceholder('Sesi video call aktif di halaman ini.', false);
    });

    jitsiApi.addListener('readyToClose', () => {
      endVideoCall(false);
    });
  } catch (error) {
    console.error(error);
    disposeJitsiApi();
    setVideoPlaceholder('Jitsi Meet gagal dimuat. Anda tetap bisa membuka link room secara manual.', true);
    document.getElementById('video-subtitle').textContent = 'Gagal memuat Jitsi Meet';
    showToast(error.message || 'Jitsi Meet gagal dimuat.');
  }

  showToast(`Membuka room video call${roomName ? `: ${roomName}` : ''}`);
}

function endVideoCall(showToastMessage = true) {
  const overlay = document.getElementById('video-overlay');
  overlay.classList.remove('active');
  document.body.style.overflow = '';
  document.getElementById('video-subtitle').textContent = 'Sesi video call ditutup.';
  document.getElementById('video-timer').style.display = 'none';
  document.getElementById('video-timer').textContent = '00:00';
  setVideoPlaceholder('Sesi video call ditutup.', true);
  disposeJitsiApi();
  activeVideoSessionId = null;

  if (showToastMessage) {
    showToast('Sesi video call ditutup.');
  }
}

document.querySelectorAll('.consult-item').forEach((button) => {
  button.addEventListener('click', () => openSession(Number(button.dataset.sessionId)));
});

document.getElementById('consult-search').addEventListener('input', function () {
  const keyword = this.value.trim().toLowerCase();
  document.querySelectorAll('.consult-item').forEach((button) => {
    const haystack = button.dataset.search || '';
    button.style.display = haystack.includes(keyword) ? 'flex' : 'none';
  });
});

document.getElementById('msg-input').addEventListener('keydown', handleChatKey);
document.getElementById('msg-input').addEventListener('input', function () {
  autoResize(this);
});
document.getElementById('send-message').addEventListener('click', sendMessage);
document.getElementById('start-video').addEventListener('click', startVideoCall);
document.getElementById('end-video').addEventListener('click', endVideoCall);
document.getElementById('open-video-external').addEventListener('click', function () {
  const roomUrl = this.dataset.url || consultationMap[currentSessionId]?.video_call_url || '';
  if (!roomUrl) {
    showToast('Link video call belum tersedia.');
    return;
  }

  window.open(roomUrl, '_blank', 'noopener,noreferrer');
});

setActiveSessionButton();
updateHeader(consultationMap[currentSessionId]);
updateVideoIdentity(consultationMap[currentSessionId]);
updateVideoButtonState(consultationMap[currentSessionId]);
startPolling();
scrollMessagesToBottom();
</script>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>

