@php $embedded = $embedded ?? false; @endphp
<div class="chat-widget {{ $embedded ? 'embedded' : '' }}" data-send-url="{{ route('chatbot.send') }}">
    @if (! $embedded)
        <button type="button" class="chat-fab" aria-label="Open chat assistant">
            <i class="bi bi-robot"></i>
            <span class="fab-badge"></span>
        </button>
    @endif

    <div class="chat-panel">
        <div class="chat-header d-flex align-items-center gap-2">
            <span class="bot-avatar"><i class="bi bi-robot"></i></span>
            <div>
                <div class="fw-bold">Jobot — AI Assistant</div>
                <div class="small opacity-75"><i class="bi bi-circle-fill me-1" style="font-size:.5rem;"></i>Online</div>
            </div>
            @if (! $embedded)
                <button type="button" class="btn-close btn-close-white ms-auto chat-close" aria-label="Close"></button>
            @endif
        </div>

        <div class="chat-messages">
            @if (isset($history) && $history->isNotEmpty())
                @foreach ($history as $msg)
                    <div class="chat-bubble user">{{ $msg->message }}</div>
                    <div class="chat-bubble bot">{{ $msg->response }}</div>
                @endforeach
            @else
                @auth
                    @if (auth()->user()->isAdmin())
                        <div class="chat-bubble bot">
                            Hi! I'm Jobot, your admin assistant. I can show you all candidates, all applications, platform statistics, and help you manage job listings. What would you like to know?
                        </div>
                    @else
                        <div class="chat-bubble bot">
                            Hi! I'm Jobot, your job assistant. I can recommend jobs based on your skills, show you available openings, and help you manage your applications. What can I help you with?
                        </div>
                    @endif
                @else
                    <div class="chat-bubble bot">
                        Hi! I'm Jobot, your AI job assistant. I can show you available jobs, filter by category or location, and answer questions about the platform's data. What can I help you with?
                    </div>
                @endauth
            @endif
        </div>

        <div class="chat-chips">
            @auth
                @if (auth()->user()->isAdmin())
                    <button type="button" class="chip">How many candidates are registered?</button>
                    <button type="button" class="chip">Show all applications</button>
                    <button type="button" class="chip">List all available jobs</button>
                    <button type="button" class="chip">How do I add a new job?</button>
                    <button type="button" class="chip">How do I delete a job?</button>
                @else
                    <button type="button" class="chip">What are the best jobs for me?</button>
                    <button type="button" class="chip">Show me remote jobs</button>
                    <button type="button" class="chip">How many jobs are open?</button>
                    <button type="button" class="chip">Edit my profile</button>
                @endif
            @endauth
            @guest
                <button type="button" class="chip">What are the best jobs?</button>
                <button type="button" class="chip">How many jobs are open?</button>
                <button type="button" class="chip">Show me remote jobs</button>
            @endguest
        </div>

        <form class="chat-input-row chat-form">
            <div class="d-flex gap-2">
                <input type="text" class="form-control chat-input" placeholder="Type your message..." autocomplete="off" required>
                <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i></button>
            </div>
        </form>
    </div>
</div>
