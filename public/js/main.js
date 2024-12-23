
function createNotification(msg, type) {
    if (!type || type !== 'error') {
        type = 'info';
    }

    let notification = document.createElement('div');
    notification.classList.add('notify-item', `notify-${type}`);
    

    let icon = document.createElement('div');
    icon.classList.add('notify-icon');
    let iconContent = type === 'info' 
        ? '<i class="far fa-smile-beam"></i>' 
        : '<i class="fas fa-frown-open"></i>';
    icon.innerHTML = iconContent;
    notification.appendChild(icon);


    let messageContainer = document.createElement('div');
    messageContainer.classList.add('notify-message');
    
    let title = document.createElement('div');
    title.classList.add('notify-item-title');
    title.textContent = (type === 'error' ? 'Error:' : 'Información:');
    messageContainer.appendChild(title);

    let message = document.createElement('div');
    message.textContent = msg;
    messageContainer.appendChild(message);

    notification.appendChild(messageContainer);


    let closeButton = document.createElement('div');
    closeButton.classList.add('notify-close');
    closeButton.textContent = 'Cerrar';
    notification.appendChild(closeButton);


    document.getElementById('notification-container').prepend(notification);


    showNotifications();


    let dismissInterval = setTimeout(function () {
        dismissNotification(notification, dismissInterval);
    }, 5000);

    closeButton.addEventListener('click', function () {
        dismissNotification(notification, dismissInterval);
    });
}


function showNotifications() {
    const notifications = document.querySelectorAll('.notify-item');
    notifications.forEach(notification => {
        notification.style.transition = 'top 0.2s, opacity 0.2s';
        notification.style.top = '0';
        notification.style.opacity = '0.85';
    });
}


function dismissNotification(el, dismissInterval) {
    el.style.transition = 'margin-left 0.2s, opacity 0.2s';
    el.style.marginLeft = '-100%';
    el.style.opacity = '0';

    el.addEventListener('transitionend', function () {
        el.remove();
    });

    if (dismissInterval) {
        clearTimeout(dismissInterval);
    }
}
