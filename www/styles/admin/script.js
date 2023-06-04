[...document.getElementsByClassName("deleteHref")].forEach((el) => {
    el.addEventListener("click", function (ev) {
        if(!confirm("Jste si jistí?")) ev.preventDefault();
    })
})