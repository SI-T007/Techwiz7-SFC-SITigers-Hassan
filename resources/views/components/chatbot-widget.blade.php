<!-- Floating AI Chatbot Launcher Widget -->
<div class="position-fixed bottom-0 end-0 p-3 p-md-4" style="z-index: 1040;">
    <a href="{{ route('chatbot.index') }}" 
       class="btn rounded-circle shadow-lg d-flex align-items-center justify-content-center transition-all fh-chatbot-launcher" 
       style="width: 54px; height: 54px; background: linear-gradient(135deg, var(--fh-primary), var(--fh-accent-cyan)); color: #fff; border: 2px solid rgba(255,255,255,0.2);"
       title="Open Fan Assistant"
       aria-label="Open Fan Assistant">
        <i class="bi bi-robot fs-4"></i>
    </a>
</div>

<style>
.fh-chatbot-launcher {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.fh-chatbot-launcher:hover {
    transform: scale(1.08) translateY(-2px);
    box-shadow: 0 10px 25px rgba(59, 130, 246, 0.5) !important;
    color: #fff;
}
</style>
