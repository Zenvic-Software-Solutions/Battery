async function registerPush() {
    if ('serviceWorker' in navigator && 'PushManager' in window) {
        try {
            const registration = await navigator.serviceWorker.register('service-worker.js');

            const permission = await Notification.requestPermission();
            if (permission !== 'granted') {
                console.log('Notification permission denied');
                return;
            }

            const subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array('BPz7YYRAIRWV07EeF68n-vaivYU9Y14O5W67IhcY24dBypgpA7kGqeMC_NOwnouJwWQLSAV_Jy-qR2776c78MYM')
            });

            await fetch('save_subscription.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(subscription)
            });

            console.log('Push subscription saved');
        } catch (error) {
            console.error('Push registration error:', error);
        }
    }
}

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const raw = window.atob(base64);
    return Uint8Array.from([...raw].map(c => c.charCodeAt(0)));
}

document.addEventListener('DOMContentLoaded', () => {
    if ('Notification' in window && Notification.permission !== 'granted') {
        registerPush();
    }
});
