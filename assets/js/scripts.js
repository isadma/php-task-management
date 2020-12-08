document.body.onclick = function(event) {
    const target = event.target;
    if (target.className && target.className.indexOf('btn-modal') !== -1) {
        event.preventDefault();
        let modalId = target.getAttribute("data-target");
        document.getElementById(modalId).style.display = "block";
    }
    else if(target.className && target.className.indexOf('cancel') !== -1){
        event.preventDefault();
        let modalId = target.getAttribute("data-target");
        document.getElementById(modalId).style.display = "none";
    }
}

// Close the modal
closeALlModals = () => {
    for(let i = 0; i < document.getElementsByClassName('modal').length; i++) {
        document.getElementsByClassName('modal')[i].style.display = "none";
    }
}
