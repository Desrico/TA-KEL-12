/**
 * Register Service Worker and Handle Web Push Subscription
 */

const VAPID_PUBLIC_KEY = 'BANc9RgVqlg0Oau0kRon4GfLRAU6shEkZVndWOiX_j-c0MsLAWKX3wpLWZZO_P6WTJjS720x8_WKaA2IBSh8DLg';

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');

    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

function initWebPush() {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        console.warn('Push messaging is not supported');
        return;
    }

    navigator.serviceWorker.register('/sw.js')
        .then(reg => {
            console.log('Service Worker Registered!', reg);
            subscribeUser(reg);
        })
        .catch(err => console.error('Service Worker registration failed', err));
}

function subscribeUser(registration) {
    const subscribeOptions = {
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY)
    };

    return registration.pushManager.subscribe(subscribeOptions)
        .then(pushSubscription => {
            console.log('Received PushSubscription: ', JSON.stringify(pushSubscription));
            storeSubscription(pushSubscription);
            return pushSubscription;
        })
        .catch(err => {
            if (Notification.permission === 'denied') {
                console.warn('Permission for notifications was denied');
            } else {
                console.error('Failed to subscribe the user: ', err);
            }
        });
}

function storeSubscription(subscription) {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch('/subscriptions', {
        method: 'POST',
        body: JSON.stringify(subscription),
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token
        }
    })
    .then(res => res.json())
    .then(data => console.log('Subscription stored on server', data))
    .catch(err => console.error('Error storing subscription on server', err));
}

// Start the process
if (Notification.permission === 'granted') {
    initWebPush();
} else if (Notification.permission !== 'denied') {
    // Optional: Show a button or UI to trigger permission request
    // For now, let's just wait for user action or trigger on some event
}
