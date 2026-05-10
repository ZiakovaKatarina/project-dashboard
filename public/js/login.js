document.getElementById('submit').addEventListener('click', validate_login);

async function validate_login(event) {
    event.preventDefault();

    document.getElementById('submit').disabled = true;
    var username = document.getElementById('username').value;
    var password = document.getElementById('password').value;

    var response = await fetch('?c=auth&a=login',
        {
            method: "POST",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "Content-type": "application/json",
                "Accept": "application/json",
            },
            body: JSON.stringify({
                username: username,
                password: password
            })
        }
    );
    var data = await response.json();
    document.getElementById('submit').disabled = false;
    
    if (data.success) {
        // source: https://stackoverflow.com/questions/503093/how-do-i-redirect-to-another-webpage
        // source: https://www.w3schools.com/howto/howto_js_redirect_webpage.asp
        window.location.href = '?c=project&a=index';
        // window.location.replace('?c=project&a=index');
    } else {
        document.getElementById('errors').innerText = 'Nesprávny email alebo heslo.'
    }
}