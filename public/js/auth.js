function validate_registration(event) {
    const first_name = document.getElementById("first_name").value.trim().length;
    const last_name = document.getElementById("last_name").value.trim().length;
    const email = document.getElementById("email").value.trim();
    const password1 = document.getElementById("password1").value;
    const password2 = document.getElementById("password2").value;
    if (first_name < 3) {
        alert("Krstné meno je príliš krátke. Musí obsahovať minimálne 3 neprázdne znaky.")
        event.preventDefault();
        return;
    }
    if (first_name > 100) {
        alert("Krstné meno je príliš dlhé. Musí obsahovať maximálne 100 neprázdnych znakov.")
        event.preventDefault();
        return;
    }
    if (last_name < 3) {
        alert("Priezvisko je príliš krátke. Musí obsahovať minimálne 3 neprázdne znaky.")
        event.preventDefault();
        return;
    }
    if (last_name > 100) {
        alert("Priezvisko je príliš dlhé. Musí obsahovať maximálne 100 neprázdnych znakov.")
        event.preventDefault();
        return;
    }
    if (password1.length < 5) {
        alert("Heslo je príliš krátke. Musí obsahovať minimálne 5 neprázdne znaky.")
        event.preventDefault();
        return;
    }
    if (password1.length > 250) {
        alert("Heslo je príliš dlhé. Musí obsahovať maximálne 250 neprázdnych znakov.")
        event.preventDefault();
        return;
    }
    if (password1 !== password2) {
        alert("Heslá sa nezhodujú. Pre registráciu sa musia zhodovať.")
        event.preventDefault();
        return;
    }
    if (email.length > 0 && !email.includes('@')) {
        alert("Email nie je v platnom stave.")
        event.preventDefault();
        return;
    } 
    if (email.length > 250) {
        alert("Email je príliš dlhý. Musí obsahovať maximálne 250 neprázdne znaky.")
        event.preventDefault();
        return;
    }
}