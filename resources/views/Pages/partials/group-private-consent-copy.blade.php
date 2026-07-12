@php
    $roomTitle = $consentContext['room_title'] ?? 'grup privat';
    $roomDescription = $consentContext['room_description'] ?? null;
    $inviterName = $consentContext['inviter_name'] ?? 'Konselor';
    $inviteReason = $consentContext['invite_reason']
        ?? $roomDescription
        ?? 'Grup privat ini relevan untuk pendampingan dan diskusi konseling.';
    $groupRules = [
        'Gunakan grup ini hanya untuk diskusi yang relevan dengan tujuan konseling.',
        'Dilarang melakukan spam, promosi, atau mengirim pesan berulang yang mengganggu anggota lain.',
        'Gunakan bahasa yang sopan. Perkataan kasar, menghina, merendahkan, atau memancing konflik tidak diperbolehkan.',
        'Jaga privasi grup. Jangan menyebarkan isi percakapan atau identitas anggota tanpa izin.',
        'Hindari membagikan data pribadi sensitif jika tidak benar-benar diperlukan untuk proses pendampingan.',
        'Ikuti arahan konselor selama diskusi berlangsung agar komunikasi tetap aman dan terarah.',
    ];
@endphp

<div class="group-consent-meta">
  <div class="group-consent-meta-item">
    <span class="group-consent-meta-label">Grup</span>
    <strong>{{ $roomTitle }}</strong>
    @if(filled($roomDescription))
      <span>{{ $roomDescription }}</span>
    @endif
  </div>
  <div class="group-consent-meta-item">
    <span class="group-consent-meta-label">Pengundang</span>
    <strong>{{ $inviterName }}</strong>
  </div>
  <div class="group-consent-meta-item">
    <span class="group-consent-meta-label">Alasan diundang</span>
    <strong>{{ $inviteReason }}</strong>
  </div>
</div>

<div class="group-consent-rules">
  <h3>Aturan Sebelum Bergabung</h3>
  <ul>
    @foreach($groupRules as $rule)
      <li>{{ $rule }}</li>
    @endforeach
  </ul>
</div>
