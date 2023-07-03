window.onload = init;

function init(){
    setTimeout(removeAlert, 4000);
}

function removeAlert(){
    for (let alert of document.getElementsByClassName('alert')) {
        alert.remove();
    }
}