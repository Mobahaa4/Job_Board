(function () {
    function initWidget(widget) {
        var fab = widget.querySelector('.chat-fab');
        var panel = widget.querySelector('.chat-panel');
        var form = widget.querySelector('.chat-form');
        var input = widget.querySelector('.chat-input');
        var messages = widget.querySelector('.chat-messages');
        var closeBtn = widget.querySelector('.chat-close');
        var sendUrl = widget.getAttribute('data-send-url') || '/chatbot/send';
        var csrf = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';

        function scrollBottom() {
            if (messages) messages.scrollTop = messages.scrollHeight;
        }

        function addBubble(text, who) {
            var div = document.createElement('div');
            div.className = 'chat-bubble ' + (who === 'user' ? 'user' : 'bot');
            div.textContent = text;
            if (messages) {
                messages.appendChild(div);
                scrollBottom();
            }
        }

        if (fab) {
            fab.addEventListener('click', function () {
                widget.classList.toggle('open');
                scrollBottom();
            });
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                widget.classList.remove('open');
            });
        }

        if (!form || !input) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var message = input.value.trim();
            if (!message) return;

            addBubble(message, 'user');
            input.value = '';
            input.focus();

            var typing = document.createElement('div');
            typing.className = 'chat-bubble bot typing';
            typing.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Jobot is typing...';
            messages.appendChild(typing);
            scrollBottom();

            fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({ message: message }),
            })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    typing.remove();
                    addBubble(data.reply, 'bot');
                })
                .catch(function () {
                    typing.remove();
                    addBubble('Sorry, something went wrong. Please try again.', 'bot');
                });
        });

        var chips = widget.querySelectorAll('.chat-chips .chip');
        Array.prototype.forEach.call(chips, function (chip) {
            chip.addEventListener('click', function () {
                input.value = chip.textContent;
                form.dispatchEvent(new Event('submit'));
            });
        });

        scrollBottom();
    }

    function initAll() {
        var widgets = document.querySelectorAll('.chat-widget');
        Array.prototype.forEach.call(widgets, initWidget);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
})();
