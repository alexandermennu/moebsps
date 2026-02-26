/**
 * Live Notifications & Messages Polling
 * Polls the server every 10 seconds for new notifications/messages
 * and displays toast popups + updates badge counts in real-time.
 */
(function () {
    'use strict';

    const POLL_INTERVAL = 10000; // 10 seconds
    let lastTimestamp = new Date().toISOString();
    let toastContainer = null;

    function init() {
        // Only run for authenticated users (check if poll endpoint exists)
        const pollUrl = document.querySelector('meta[name="poll-url"]');
        if (!pollUrl) return;

        createToastContainer();
        poll();
        setInterval(poll, POLL_INTERVAL);
    }

    function createToastContainer() {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'fixed top-4 right-4 z-50 space-y-3 pointer-events-none';
        toastContainer.style.maxWidth = '380px';
        document.body.appendChild(toastContainer);
    }

    async function poll() {
        const pollUrl = document.querySelector('meta[name="poll-url"]');
        if (!pollUrl) return;

        try {
            const response = await fetch(`${pollUrl.content}?since=${encodeURIComponent(lastTimestamp)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) return;

            const data = await response.json();

            // Update badge counts
            updateBadges(data.unread_notifications, data.unread_messages);

            // Show toast popups for new items
            if (data.new_notifications && data.new_notifications.length > 0) {
                data.new_notifications.forEach(function (n) {
                    showToast({
                        type: 'notification',
                        icon: getNotificationIcon(n.type),
                        title: n.title,
                        body: n.message,
                        link: n.link,
                        time: n.time,
                    });
                });
            }

            if (data.new_messages && data.new_messages.length > 0) {
                data.new_messages.forEach(function (m) {
                    showToast({
                        type: 'message',
                        icon: '✉️',
                        title: m.sender,
                        body: m.subject + ': ' + m.preview,
                        link: m.link,
                        time: m.time,
                    });
                });
            }

            // Update timestamp
            if (data.timestamp) {
                lastTimestamp = data.timestamp;
            }

        } catch (e) {
            // Silently fail - don't disrupt the user
        }
    }

    function updateBadges(notifCount, msgCount) {
        // Top bar notification badge
        const notifBadges = document.querySelectorAll('[data-badge="notifications"]');
        notifBadges.forEach(function (badge) {
            if (notifCount > 0) {
                badge.textContent = notifCount;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        });

        // Top bar message badge
        const msgBadges = document.querySelectorAll('[data-badge="messages"]');
        msgBadges.forEach(function (badge) {
            if (msgCount > 0) {
                badge.textContent = msgCount;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        });

        // Sidebar notification badge
        const sidebarNotif = document.querySelectorAll('[data-sidebar-badge="notifications"]');
        sidebarNotif.forEach(function (badge) {
            if (notifCount > 0) {
                badge.textContent = notifCount;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        });

        // Sidebar message badge
        const sidebarMsg = document.querySelectorAll('[data-sidebar-badge="messages"]');
        sidebarMsg.forEach(function (badge) {
            if (msgCount > 0) {
                badge.textContent = msgCount;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        });
    }

    function getNotificationIcon(type) {
        switch (type) {
            case 'overdue': return '⚠️';
            case 'escalation': return '🔺';
            case 'approval': return '✅';
            case 'rejection': return '❌';
            case 'message': return '✉️';
            default: return '🔔';
        }
    }

    function showToast(opts) {
        // Play notification sound (subtle)
        playNotificationSound();

        const toast = document.createElement('div');
        toast.className = 'pointer-events-auto bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden transform transition-all duration-300 translate-x-full opacity-0';
        toast.innerHTML = `
            <div class="p-4">
                <div class="flex items-start gap-3">
                    <span class="text-xl flex-shrink-0 mt-0.5">${opts.icon}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900">${escapeHtml(opts.title)}</p>
                        <p class="text-sm text-gray-600 mt-0.5 line-clamp-2">${escapeHtml(opts.body)}</p>
                        <p class="text-xs text-gray-400 mt-1">${escapeHtml(opts.time)}</p>
                    </div>
                    <button onclick="this.closest('[data-toast]').remove()" class="text-gray-400 hover:text-gray-600 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            ${opts.link ? `<a href="${opts.link}" class="block px-4 py-2 bg-gray-50 text-xs text-slate-600 hover:text-slate-800 hover:bg-gray-100 border-t border-gray-100">View →</a>` : ''}
        `;
        toast.setAttribute('data-toast', '');

        toastContainer.appendChild(toast);

        // Animate in
        requestAnimationFrame(function () {
            toast.classList.remove('translate-x-full', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');
        });

        // Auto dismiss after 6 seconds
        setTimeout(function () {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(function () {
                if (toast.parentNode) toast.remove();
            }, 300);
        }, 6000);
    }

    function playNotificationSound() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = ctx.createOscillator();
            const gain = ctx.createGain();
            oscillator.connect(gain);
            gain.connect(ctx.destination);
            oscillator.frequency.value = 800;
            oscillator.type = 'sine';
            gain.gain.value = 0.1;
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
            oscillator.start(ctx.currentTime);
            oscillator.stop(ctx.currentTime + 0.3);
        } catch (e) {
            // Audio not supported, skip
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Start when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
