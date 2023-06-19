function translatedInputs(event, element) {
    let inputsInForm = element.querySelectorAll("[data-translate-for]");
    let arrInput = {}; // todo validation security
    inputsInForm.forEach((input) => {
        let originalInputName = input.dataset["translateFor"];
        let code = input.dataset["translateCode"];
        if (!arrInput[originalInputName]) arrInput[originalInputName] = {}
        if(input.value !== "") arrInput[originalInputName][code] = input.value;
    })
    Object.keys(arrInput).forEach((k) => {
        element.querySelectorAll("[data-translate-id]").forEach((originalInput) => {
            if(k === originalInput.name) {
                originalInput.value = JSON.stringify(arrInput[k]);
                console.log(originalInput.value);
            }
        })
    });
}