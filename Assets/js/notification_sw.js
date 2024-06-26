self.addEventListener('notificationclick', event => {
    // Close the notification popout
    event.notification.close();

    // This looks to see if the current is already open and focuses if it is
    event.waitUntil(
        clients
            .matchAll({includeUncontrolled: true, type: 'window'})
            .then((clientList) => {
                // If a Window tab matching the targeted URL already exists, focus that
                const hadWindowToFocus = clientList.some((windowClient) => {
                    return windowClient.url === event.notification.data.url
                        ? (windowClient.focus(), true)
                        : false;
                });
                // Otherwise, open a new tab to the applicable URL and focus it
                if (!hadWindowToFocus) {
                    clients
                        .openWindow(event.notification.data.url)
                        .then((windowClient) => (windowClient ? windowClient.focus() : null));
                }
            }),
    );
});
