<?php

header('Content-Type: application/javascript');
header('Service-Worker-Allowed: /');

?>

self.addEventListener('push', function (event) {
    if (!Notification) {
        console.warn('Notifications are not available in your browser.');
        return;
    }

    if (Notification.permission !== 'granted') {
        Notification.requestPermission();
    } else {
        const notification_data = event.data.json();
        const options = {
            body: notification_data.content,
            icon: location.origin + '/plugins/TodoNotes/Assets/img/icon.png',
            badge: location.origin + '/plugins/TodoNotes/Assets/img/badge.png',
            data: {url: notification_data.link},
            timestamp: notification_data.timestamp * 1000,
            vibrate: [200, 100, 200, 100, 200, 100, 200],
        };
        event.waitUntil(self.registration.showNotification(notification_data.title, options));
    }
});

self.addEventListener('notificationclick', function(event) {
    // Close the notification popout
    event.notification.close();

    // This looks to see if the current is already open and focuses if it is
    event.waitUntil(
        clients
            .matchAll({includeUncontrolled: true, type: 'window'})
            .then(function (clientList) {
                // If a Window tab matching the targeted URL already exists, focus that
                const hadWindowToFocus = clientList.some(function (windowClient) {
                    return windowClient.url === event.notification.data.url
                        ? (windowClient.focus(), true)
                        : false;
                });
                // Otherwise, open a new tab to the applicable URL and focus it
                if (!hadWindowToFocus) {
                    clients
                        .openWindow(event.notification.data.url)
                        .then(function (windowClient) {
                            return (windowClient ? windowClient.focus() : null)
                        });
                }
            }),
    );
});
