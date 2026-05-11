// public/sw.js
self.addEventListener('push', function (event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    const data = event.data.json();
    const title = data.title || 'Notifikasi Baru';
    const options = {
        body: data.body || 'Ada pesan baru untuk Anda.',
        icon: '/favicon.ico',
        badge: '/favicon.ico',
        data: {
            url: data.action_url || '/'
        }
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    event.waitUntil(
        clients.openWindow(event.notification.data.url)
    );
});
