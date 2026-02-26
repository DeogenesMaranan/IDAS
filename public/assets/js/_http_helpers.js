export function ajaxPost(url, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.onload = function() {
        callback(xhr.responseText, xhr.status);
    };
    xhr.onerror = function() {
        callback(null, 500);
    };
    xhr.send(new URLSearchParams(data).toString());
}

export function ajaxGet(url, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.onload = function() {
        callback(xhr.responseText, xhr.status);
    };
    xhr.onerror = function() {
        callback(null, 500);
    };
    xhr.send();
}
