function edit_user_in_project(user_id) {
    document.getElementById('form_change_user_rights_' + user_id).style.display = 'block';
    document.getElementById('saved_right_' + user_id).style.display = 'none';
}

function cancel_editing_user_in_project(user_id) {
    document.getElementById('form_change_user_rights_' + user_id).style.display = 'none';
    document.getElementById('saved_right_' + user_id).style.display = 'inline';
}

function validate_project(event) {
    const name = document.getElementById("name").value.trim().length;
    if (name < 3) {
        alert("Názov projektu je príliš krátky. Musí obsahovať minimálne 3 neprázdne znaky.")
        event.preventDefault();
        return;
    }

    if (name > 100) {
        alert("Názov projektu je príliš dlhý. Musí obsahovať maximálne 100 neprázdnych znakov.")
        event.preventDefault();
        return;
    }
    
    const description = document.getElementById("description").value.trim().length;
    if (description > 500) {
        alert("Popis projektu je príliš dlhý. Musí obsahovať maximálne 500 neprázdnych znakov.")
        event.preventDefault();
        return;
    }

    const status = document.getElementById("status").value.trim();
    if (!['C', 'D', 'R', 'P'].includes(status)) {
        alert("Status obsahuje neznámu hodnotu.")
        event.preventDefault();
        return;
    }

    const deadline = document.getElementById("deadline").value;
    if (deadline !== "") {
        const deadlineDate = new Date(deadline);
        if (isNaN(deadlineDate.getTime())) {
            alert("Termín obsahuje neznámu hodnotu.")
            event.preventDefault();
            return;
        }
    }

    const submission = document.getElementById("submission").value;
    if (submission !== "") {
        const submissionDate = new Date(submission);
        if (isNaN(submissionDate.getTime())) {
            alert("Dátum odovzdania obsahuje neznámu hodnotu.")
            event.preventDefault();
            return;
        }
    }
}