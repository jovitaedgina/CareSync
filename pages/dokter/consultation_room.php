<?php
require_once '../../includes/staff_portal_helpers.php';

$requestedConsultationId = (int) ($_GET['consultation'] ?? $_GET['consultation_id'] ?? 0);
$room = getDoctorConsultationRoomData($pdo, (int) (currentUser()['id'] ?? 0), $requestedConsultationId);
$consultations = $room['consultations'];
$selected = $room['selected'];
$messages = $room['messages'];

include '../../includes/header_dokter.php';
?>

<style>
    .doctor-room-bg {
        background:
            radial-gradient(circle at top left, rgba(59, 130, 246, 0.08), transparent 26%),
            linear-gradient(180deg, #f8fbff 0%, #f8fafc 42%, #eef4ff 100%);
    }
    .doctor-room-scroll::-webkit-scrollbar {
        width: 8px;
    }
    .doctor-room-scroll::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, .42);
        border-radius: 999px;
    }
</style>

<div class="doctor-room-bg min-h-full p-4 sm:p-6 lg:p-8" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="max-w-[1500px] mx-auto">
        <div class="mb-6 flex flex-col lg:flex-row lg:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Ruang Konsultasi</h1>
                <p class="text-slate-500 mt-1">Timer, chat, video call, diagnosis, dan review pasien semuanya tersinkron ke sesi konsultasi yang sama.</p>
            </div>
            <div class="bg-white px-4 py-3 rounded-2xl border border-slate-200 shadow-sm text-sm font-bold text-slate-700">
                <?= count($consultations) ?> sesi terhubung
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-[390px_minmax(0,1fr)] gap-6" x-data="doctorConsultationApp()">
            <aside class="bg-white rounded-[28px] border border-slate-200 shadow-sm overflow-hidden min-h-[760px]">
                <div class="p-5 border-b border-slate-100">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-3.5 text-slate-400"></i>
                        <input x-model="search" type="text" placeholder="Cari nama pasien..." class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-2 border-b border-slate-100 bg-slate-50/70 px-3">
                    <button type="button" @click="activeTab = 'aktif'" class="relative py-4 text-sm font-bold transition" :class="activeTab === 'aktif' ? 'text-blue-600' : 'text-slate-500 hover:text-slate-700'">
                        Aktif
                        <span class="ml-1 inline-flex items-center justify-center min-w-5 h-5 px-1 rounded-full text-[10px] font-extrabold" :class="activeTab === 'aktif' ? 'bg-blue-100 text-blue-700' : 'bg-slate-200 text-slate-600'" x-text="activeSessions.length"></span>
                        <span x-show="activeTab === 'aktif'" class="absolute left-2 right-2 -bottom-px h-0.5 bg-blue-600 rounded-full"></span>
                    </button>
                    <button type="button" @click="activeTab = 'antrian'" class="relative py-4 text-sm font-bold transition" :class="activeTab === 'antrian' ? 'text-blue-600' : 'text-slate-500 hover:text-slate-700'">
                        Antrean
                        <span class="ml-1 inline-flex items-center justify-center min-w-5 h-5 px-1 rounded-full text-[10px] font-extrabold" :class="activeTab === 'antrian' ? 'bg-blue-100 text-blue-700' : 'bg-slate-200 text-slate-600'" x-text="queueSessions.length"></span>
                        <span x-show="activeTab === 'antrian'" class="absolute left-2 right-2 -bottom-px h-0.5 bg-blue-600 rounded-full"></span>
                    </button>
                </div>

                <div class="max-h-[calc(100vh-300px)] overflow-y-auto doctor-room-scroll p-2">
                    <template x-if="visibleSessions.length === 0">
                        <div class="px-6 py-12 text-center text-sm text-slate-500">
                            Tidak ada sesi yang cocok dengan pencarian.
                        </div>
                    </template>

                    <template x-for="session in visibleSessions" :key="session.id">
                        <button @click="openSession(session.id)" type="button" class="w-full text-left px-4 py-4 transition rounded-2xl mb-2 border border-transparent hover:bg-blue-50/60" :class="activeId === session.id ? 'bg-blue-50 border-blue-100 shadow-sm' : ''">
                            <div class="flex items-start gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between gap-2 items-start">
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-slate-900 truncate" x-text="session.display_name"></p>
                                            <p class="text-[11px] font-semibold text-pink-500 mt-1" x-text="`${session.patient_age || ''}${session.patient_age ? ' • ' : ''}${session.display_subtitle}`"></p>
                                        </div>
                                        <span class="text-[10px] font-bold text-blue-500 whitespace-nowrap" x-text="session.last_message_label"></span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-2 truncate" x-text="session.preview"></p>
                                </div>
                            </div>
                        </button>
                    </template>
                </div>
            </aside>

            <section class="bg-white rounded-[28px] border border-slate-200 shadow-sm overflow-hidden min-h-[760px] flex flex-col">
                <template x-if="!activeSession">
                    <div class="flex-1 flex items-center justify-center text-center px-6">
                        <div>
                            <div class="w-20 h-20 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-4 text-3xl">
                                <i class="fa-solid fa-comment-medical"></i>
                            </div>
                            <h2 class="text-xl font-bold text-slate-900">Belum ada sesi dipilih</h2>
                            <p class="text-sm text-slate-500 mt-2">Pilih sesi dari panel kiri untuk memulai konsultasi.</p>
                        </div>
                    </div>
                </template>

                <template x-if="activeSession">
                    <div class="flex-1 flex flex-col min-h-0">
                        <div class="px-6 py-5 border-b border-slate-100 flex flex-col xl:flex-row xl:items-center justify-between gap-4 bg-white">
                            <div class="flex items-center gap-4 min-w-0">
                                <div class="min-w-0">
                                    <h2 class="text-lg font-bold text-slate-900 truncate" x-text="activeSession.display_name"></h2>
                                    <p class="text-xs text-pink-500 font-medium" x-text="`${activeSession.patient_age || ''}${activeSession.patient_age ? ' • ' : ''}${activeSession.display_subtitle}`"></p>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center justify-end gap-3">
                                <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-red-50 text-red-500 font-bold text-sm border border-red-100" x-show="activeSession.video_call_started_at">
                                    <i class="fa-regular fa-clock text-xs"></i>
                                    <span x-text="sessionTimerLabel"></span>
                                </div>
                                <button @click="startVideoCall()" type="button" class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 transition flex items-center justify-center disabled:bg-slate-100 disabled:text-slate-300 disabled:cursor-not-allowed" :disabled="activeSession.status === 'Selesai'" title="Mulai Video Call">
                                    <i class="fa-solid fa-video"></i>
                                </button>
                                <button @click="goToPrescription()" type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2.5 rounded-xl shadow-sm transition flex items-center gap-2 disabled:bg-gray-300 disabled:cursor-not-allowed" :disabled="!activeSession.video_call_finished">
                                    <i class="fa-solid fa-file-prescription"></i> Akhiri & Buat Resep
                                </button>
                            </div>
                        </div>

                        <div class="px-6 py-3 bg-slate-50 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                            <div class="text-sm text-slate-500">
                                <span class="font-semibold text-slate-700">Jadwal:</span>
                                <span x-text="activeSession.scheduled_label"></span>
                            </div>
                            <div class="flex items-center gap-3 flex-wrap">
                                <a :href="activeSession.video_call_url || '#'" target="_blank" class="text-xs font-bold text-blue-700 hover:text-blue-600 break-all no-underline" x-text="activeSession.video_call_url ? 'Buka Link Video Call' : 'Belum tersedia'"></a>
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold" :class="activeSession.status === 'Berjalan' ? 'bg-green-100 text-green-700' : (activeSession.status === 'Selesai' ? 'bg-slate-200 text-slate-700' : 'bg-amber-100 text-amber-700')">
                                    <i class="fa-solid fa-circle text-[8px]"></i> <span x-text="activeSession.status"></span>
                                </span>
                            </div>
                        </div>

                        <div x-show="!activeSession.video_call_finished" class="px-6 py-3 bg-amber-50 border-b border-amber-100 text-sm font-medium text-amber-700">
                            Diagnosis dan resep baru bisa diisi setelah dokter menyelesaikan video call.
                            <button @click="endVideoCall()" type="button" class="ml-2 inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white font-bold px-3 py-2 rounded-xl shadow-sm transition" x-show="activeSession.status === 'Berjalan' && !activeSession.video_call_finished">
                                <i class="fa-solid fa-phone-slash"></i> Selesaikan Video
                            </button>
                        </div>

                        <div x-show="activeSession.video_call_started_at" class="px-6 py-3 bg-white">
                            <div class="mx-auto w-fit px-4 py-2 rounded-full bg-amber-50 border border-amber-200 text-[11px] font-extrabold uppercase tracking-wider text-amber-600 text-center" x-text="startedNoteLabel"></div>
                        </div>

                        <div class="px-6 py-3 border-b border-slate-100 text-sm font-medium" :class="activeSession && activeSession.chat_expired ? 'text-rose-700 bg-rose-50' : 'text-amber-700 bg-amber-50'">
                            <span x-text="activeSession?.chat_retention_notice || 'Riwayat chat tersedia selama konsultasi berlangsung.'"></span>
                        </div>

                        <div x-ref="messagesContainer" class="flex-1 overflow-y-auto px-6 py-6 space-y-4 bg-[#f6f8fc] doctor-room-scroll">
                            <template x-if="messages.length === 0">
                                <p class="text-sm text-slate-500 text-center py-8" x-text="activeSession && activeSession.chat_expired ? (activeSession.chat_retention_notice || 'Riwayat chat konsultasi sudah dihapus.') : 'Belum ada pesan pada sesi ini.'"></p>
                            </template>

                            <template x-for="message in messages" :key="message.id">
                                <div :class="message.sender_role && message.sender_role.toLowerCase() === 'dokter' ? 'justify-end' : 'justify-start'" class="flex items-end gap-2">
                                    <div :class="message.sender_role && message.sender_role.toLowerCase() === 'dokter' ? 'bg-blue-600 text-white rounded-br-md' : 'bg-white border border-slate-200 text-slate-800 rounded-bl-md'" class="max-w-[78%] rounded-2xl px-4 py-3 shadow-sm">
                                        <div class="flex items-center gap-2 mb-1" :class="message.sender_role && message.sender_role.toLowerCase() === 'dokter' ? 'justify-end' : ''">
                                            <span class="text-[11px] font-bold opacity-80" x-text="message.sender_name"></span>
                                            <span class="text-[10px] opacity-70" x-text="message.time"></span>
                                        </div>
                                        <p class="text-sm whitespace-pre-wrap m-0" x-text="message.body"></p>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <form @submit.prevent="sendMessage" class="p-4 border-t border-slate-100 bg-white">
                            <div class="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-2xl px-3 py-2">
                                <button type="button" class="w-9 h-9 rounded-xl text-slate-400 hover:text-slate-600 transition flex items-center justify-center" disabled>
                                    <i class="fa-solid fa-paperclip"></i>
                                </button>
                                <textarea x-model="draft" rows="1" :placeholder="activeSession && activeSession.chat_expired ? 'Riwayat chat sudah dihapus setelah 24 jam.' : (activeSession && activeSession.status === 'Selesai' ? 'Sesi konsultasi sudah selesai.' : 'Ketik balasan untuk pasien...')" class="flex-1 resize-none bg-transparent px-2 py-2 outline-none text-sm disabled:text-slate-400" :disabled="activeSession && (activeSession.status === 'Selesai' || activeSession.chat_expired)"></textarea>
                                <button type="submit" class="w-11 h-11 rounded-full bg-blue-600 hover:bg-blue-700 text-white transition shadow-sm flex items-center justify-center disabled:bg-slate-300 disabled:cursor-not-allowed" :disabled="activeSession && (activeSession.status === 'Selesai' || activeSession.chat_expired)">
                                    <i class="fa-solid fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </template>
            </section>
        </div>
    </div>
</div>

<script>
function doctorConsultationApp() {
    return {
        baseUrl: '<?= BASE_URL ?>',
        sessions: <?= json_encode($consultations, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        activeId: <?= (int) ($selected['id'] ?? 0) ?>,
        activeSession: <?= json_encode($selected, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        messages: <?= json_encode($messages, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        search: '',
        activeTab: 'aktif',
        draft: '',
        poller: null,
        timerInterval: null,
        sessionTimerLabel: '',
        startedNoteLabel: '',
        lastMessageId: <?= $messages ? max(array_map(static fn($item) => (int) $item['id'], $messages)) : 0 ?>,
        get searchedSessions() {
            return this.sessions.filter(session =>
                `${session.display_name} ${session.specialization} ${session.preview}`.toLowerCase().includes(this.search.toLowerCase())
            );
        },
        get activeSessions() {
            return this.searchedSessions.filter(session => session.status === 'Berjalan');
        },
        get queueSessions() {
            return this.searchedSessions.filter(session => session.status !== 'Berjalan');
        },
        get visibleSessions() {
            return this.activeTab === 'aktif' ? this.activeSessions : this.queueSessions;
        },
        init() {
            if (this.activeId) {
                this.startPolling();
            }
            this.refreshSessionVisuals();
            this.$nextTick(() => this.scrollToBottom());
        },
        formatTimeOnly(value) {
            if (!value) return '';
            const date = new Date(String(value).replace(' ', 'T'));
            if (Number.isNaN(date.getTime())) return '';
            return new Intl.DateTimeFormat('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false, timeZone: 'Asia/Jakarta' }).format(date);
        },
        formatDuration(totalSeconds) {
            const safe = Math.max(0, Number(totalSeconds || 0));
            const hours = Math.floor(safe / 3600);
            const minutes = Math.floor((safe % 3600) / 60);
            const seconds = safe % 60;
            if (hours > 0) {
                return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            }
            return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        },
        refreshSessionVisuals() {
            if (this.timerInterval) clearInterval(this.timerInterval);
            this.sessionTimerLabel = '';
            this.startedNoteLabel = '';
            if (!this.activeSession || !this.activeSession.video_call_started_at) return;

            const started = new Date(String(this.activeSession.video_call_started_at).replace(' ', 'T'));
            const ended = this.activeSession.video_call_ended_at ? new Date(String(this.activeSession.video_call_ended_at).replace(' ', 'T')) : null;
            if (Number.isNaN(started.getTime())) return;

            this.startedNoteLabel = `SESI KONSULTASI DIMULAI (${this.formatTimeOnly(this.activeSession.video_call_started_at)} WIB)`;
            const update = () => {
                const target = ended && !Number.isNaN(ended.getTime()) ? ended : new Date();
                this.sessionTimerLabel = this.formatDuration(Math.floor((target.getTime() - started.getTime()) / 1000));
            };
            update();
            if (!ended || Number.isNaN(ended.getTime())) {
                this.timerInterval = setInterval(update, 1000);
            }
        },
        async openSession(id) {
            this.activeId = id;
            const response = await fetch(`${this.baseUrl}/api/chat/get.php?consultation_id=${id}`, { credentials: 'same-origin' });
            const result = await response.json();
            if (result.status !== 'success') {
                alert(result.message || 'Gagal memuat sesi.');
                return;
            }
            this.activeSession = result.data.consultation;
            this.sessions = this.sessions.map(session => session.id === id ? { ...session, ...result.data.consultation } : session);
            this.messages = result.data.messages || [];
            this.lastMessageId = this.messages.length ? Math.max(...this.messages.map(item => Number(item.id || 0))) : 0;
            this.refreshSessionVisuals();
            this.scrollToBottom();
            this.startPolling();
        },
        async sendMessage() {
            if (!this.activeSession || !this.draft.trim() || this.activeSession.status === 'Selesai' || this.activeSession.chat_expired) return;

            const response = await fetch(`${this.baseUrl}/api/chat/send.php`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    consultation_id: this.activeSession.id,
                    message: this.draft
                })
            });
            const result = await response.json();
            if (result.status !== 'success') {
                alert(result.message || 'Pesan gagal dikirim.');
                return;
            }

            this.messages.push(result.data);
            this.lastMessageId = Math.max(this.lastMessageId, Number(result.data.id || 0));
            this.activeSession.preview = result.data.body || this.activeSession.preview;
            this.activeSession.last_message_label = result.data.time || this.activeSession.last_message_label;
            this.sessions = this.sessions.map(session => session.id === this.activeSession.id ? { ...session, ...this.activeSession } : session);
            this.draft = '';
            this.scrollToBottom();
        },
        async startVideoCall() {
            if (!this.activeSession || this.activeSession.status === 'Selesai') return;

            const response = await fetch(`${this.baseUrl}/api/consultation/video-session.php`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ consultation_id: this.activeSession.id, action: 'start' })
            });
            const result = await response.json();
            if (result.status !== 'success') {
                alert(result.message || 'Video call gagal dimulai.');
                return;
            }

            this.activeSession = result.data.consultation;
            this.sessions = this.sessions.map(session => session.id === this.activeSession.id ? { ...session, ...this.activeSession } : session);
            this.refreshSessionVisuals();
            window.open(this.activeSession.video_call_url, '_blank', 'noopener');
        },
        async endVideoCall() {
            if (!this.activeSession || this.activeSession.status === 'Selesai') return;

            const response = await fetch(`${this.baseUrl}/api/consultation/video-session.php`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ consultation_id: this.activeSession.id, action: 'end' })
            });
            const result = await response.json();
            if (result.status !== 'success') {
                alert(result.message || 'Video call gagal diakhiri.');
                return;
            }

            this.activeSession = result.data.consultation;
            this.sessions = this.sessions.map(session => session.id === this.activeSession.id ? { ...session, ...this.activeSession } : session);
            this.refreshSessionVisuals();
            alert('Video call selesai. Anda sekarang bisa mengisi diagnosis dan resep.');
        },
        goToPrescription() {
            if (!this.activeSession || !this.activeSession.video_call_finished) return;
            window.location.href = `${this.baseUrl}/pages/dokter/prescription_form.php?consultation=${this.activeSession.id}`;
        },
        startPolling() {
            if (this.poller) clearInterval(this.poller);
            this.poller = setInterval(() => this.fetchLatest(), 2500);
        },
        async fetchLatest() {
            if (!this.activeSession) return;
            const response = await fetch(`${this.baseUrl}/api/chat/get.php?consultation_id=${this.activeSession.id}&last_id=${this.lastMessageId}`, { credentials: 'same-origin' });
            const result = await response.json();
            if (result.status !== 'success') return;
            this.activeSession = result.data.consultation;
            this.sessions = this.sessions.map(session => session.id === this.activeSession.id ? { ...session, ...this.activeSession } : session);
            this.refreshSessionVisuals();
            const latest = result.data.messages || [];
            if (latest.length) {
                latest.forEach(message => this.messages.push(message));
                this.lastMessageId = Math.max(this.lastMessageId, ...latest.map(item => Number(item.id || 0)));
                const newest = latest[latest.length - 1];
                this.activeSession.preview = newest.body || this.activeSession.preview;
                this.activeSession.last_message_label = newest.time || this.activeSession.last_message_label;
                this.sessions = this.sessions.map(session => session.id === this.activeSession.id ? { ...session, ...this.activeSession } : session);
                this.scrollToBottom();
            }
        },
        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                if (container) container.scrollTop = container.scrollHeight;
            });
        }
    };
}
</script>


