<?php

header('Content-Type: application/javascript');
header('Service-Worker-Allowed: /');

?>

var _SW_TodoNotes_BaseAppDir_ = '/';

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
            icon: _SW_TodoNotes_BaseAppDir_ + 'plugins/TodoNotes/Assets/img/icon.png',
            badge: _SW_TodoNotes_BaseAppDir_ + 'plugins/TodoNotes/Assets/img/badge.png',
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

var _SW_TodoNotes_Heartbeat_ = false;

self.addEventListener('message', function(event) {
    //console.log(event.data);
    _SW_TodoNotes_BaseAppDir_ = event.data.baseAppDir;

    function heartbeat() {
        fetch(_SW_TodoNotes_BaseAppDir_ + '?controller=TodoNotesNotificationsController&action=Heartbeat&plugin=TodoNotes', { method: 'POST' })
            .then(function(/*response*/) {
                //response.text().then(function(text) {
                //    console.log(text);
                //});
            })
            .catch(function(e) {
                console.error(e);
            });
    }

    if (!_SW_TodoNotes_Heartbeat_ && event.data.type === 'heartbeat') {
        heartbeat();
        //setInterval(heartbeat, 15 * 1000); // 15 sec
        setInterval(heartbeat, 5 * 60 * 1000); // 5 min

        console.info('[SW] Started Heartbeat');
        _SW_TodoNotes_Heartbeat_ = true;
    }
});

self.addEventListener('install', function(event) {
    self.skipWaiting(); // always activate updated SW immediately
});
