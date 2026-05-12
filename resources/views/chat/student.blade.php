@extends('layouts.master')
@section('page-title', 'Chat Sesi')

@push('styles')
<style>
  /* Minimal chat styles reusing admin chat look */
  .chat-container { max-width: 980px; margin: 2rem auto; background:#fff; border-radius:12px; overflow:hidden; }
  .chat-header{ padding:1rem; border-bottom:1px solid #eee; display:flex; justify-content:space-between; align-items:center }
  .messages { padding:1rem; min-height:300px; max-height:60vh; overflow-y:auto }
  .message { margin-bottom:.8rem }
  .message .bubble { display:inline-block; padding:.6rem .9rem; border-radius:12px; }
  .message.sent .bubble{ background:#0f766e; color:#fff; margin-left:auto }
  .message.received .bubble{ background:#f0f0f0; color:#111 }
  .chat-input{ padding: .8rem; border-top:1px solid #eee; display:flex; gap:.5rem }
</style>
@endpush

@section('konten')
@php
  $sessionActive = isset($sessionData);
  $studentName = $sessionActive ? $sessionData['nama'] : 'Peserta';
  $messages = $messages ?? collect();
  $currentUserId = auth()->id();
@endphp

<div class="chat-container">
  <div class="chat-header">
    <div>
      <h4 style="margin:0">{{ $studentName }}</h4>
      <small class="text-muted">Ruang percakapan</small>
    </div>
  </div>

  <div class="messages" id="messagesContainer">
    @forelse($messages as $message)
      @php $isSent = $message->pengirim_id === $currentUserId; @endphp
      <div class="message {{ $isSent ? 'sent' : 'received' }}">
        <div class="bubble">{{ $message->pesan }}</div>
      </div>
    @empty
      <div class="text-center text-muted py-5">Belum ada pesan. Mulai percakapan sekarang.</div>
    @endforelse
  </div>

  <form method="POST" action="{{ $sessionActive ? route('chat.student.store', $sessionData['id']) : '#' }}" class="chat-input">
    @csrf
    <input name="pesan" class="form-control" placeholder="Tulis pesan..." {{ $sessionActive ? '' : 'disabled' }}>
    <button class="btn btn-success" {{ $sessionActive ? '' : 'disabled' }}>Kirim</button>
  </form>
</div>

@push('scripts')
<script>
  const messagesContainer = document.getElementById('messagesContainer');
  if (messagesContainer) messagesContainer.scrollTop = messagesContainer.scrollHeight;
</script>
@endpush

@endsection
