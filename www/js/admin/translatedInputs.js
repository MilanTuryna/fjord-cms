function passContent() {
    let tabsInputs = document.querySelectorAll("[data-translate-for]");
    let originalInputs = document.querySelectorAll("[data-translate-id]");
    let originalInputKeyValue = {};
    originalInputs.forEach((originalInput) => {
        originalInputKeyValue[originalInput.name] = originalInput.value !== "" ? JSON.parse(originalInput.value) : {}
    });
    tabsInputs.forEach((input) => {
        let originalInputName = input.dataset["translateFor"];
        let code = input.dataset["translateCode"];

        if(originalInputKeyValue[originalInputName] && code in originalInputKeyValue[originalInputName]) {
            if(input.classList.contains("active-wysiwyg")) {
                console.log(tinyMCE.activeEditor);
                tinyMCE.get(input.id).setContent(originalInputKeyValue[originalInputName][code]);
            } else {
                input.value = originalInputKeyValue[originalInputName][code];
            }
        }
    })
}

function translatedInputs(event, element) {
    let inputsInForm = element.querySelectorAll("[data-translate-for]");
    let arrInput = {}; // todo validation security
    inputsInForm.forEach((input) => {
        let originalInputName = input.dataset["translateFor"];
        let code = input.dataset["translateCode"];
        if (!arrInput[originalInputName]) arrInput[originalInputName] = {}
        if(input.classList.contains("active-wysiwyg")) {
            if(input.value !== "") arrInput[originalInputName][code] = tinyMCE.get(input.id).getContent().trim();
        } else {
            if(input.value !== "") arrInput[originalInputName][code] = input.value;
        }
    })
    Object.keys(arrInput).forEach((k) => {
        element.querySelectorAll("[data-translate-id]").forEach((originalInput) => {
            if(k === originalInput.name) {
                originalInput.value = JSON.stringify(arrInput[k]);
            }
        })
    });
}
setTimeout(function () { // hack for TinyMCE
    passContent();
}, 500);