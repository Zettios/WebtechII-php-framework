document.addEventListener("DOMContentLoaded", function() {
    let bars = document.getElementsByClassName('editfield');
    let saveButtons = document.getElementsByClassName('save');
    let deleteButtons = document.getElementsByClassName('delete');

    for(let i = 0; i < bars.length; i++) {
        bars[i].id = i;
    }

    for(let j = 0; j < saveButtons.length; j++) {
        saveButtons[j].addEventListener('click', btn => {
            save(btn);
        });
    }

    for(let x = 0; x < deleteButtons.length; x++) {
        deleteButtons[x].addEventListener('click', btn => {
            deleteUser(btn);
        });
    }
});

function save(btn) {
    btn.preventDefault();
    let parent = btn.target.parentNode
    let id = parent.querySelector('.id').innerHTML;
    let name = parent.querySelector('.name').value;
    let email = parent.querySelector('.email').value;
    let password = parent.querySelector('.password').value;
    let role = parent.querySelector('.role').value;

    if (isPositiveInteger(role)) {
        fetch('http://127.0.0.1:8000/updateUserAdmin?user_id='+id+'&username='+name+'&email='+email+'' +
            '&password='+password.trim()+'&role='+role,
            {
                method: 'PUT',
            })
            .then( resp => {
                console.log(resp);
                return resp.json()
            } )
            .then( data => {
                if (data['status'] === 200) {
                    var r = confirm("Gegevens geupdate!");
                    if (r === true){
                        window.location.reload();
                    } else {
                        window.location.reload();
                    }
                } else if (data['status'] === 403){
                    alert("Gebruikersnaam bestaat al.");
                } else {
                    alert("Gebruikersnaam of wachtwoord is incorrect.");
                }
            })
    }
}

function deleteUser(btn) {
    btn.preventDefault();
    let parent = btn.target.parentNode
    let id = parent.querySelector('.id').innerHTML;

    fetch('http://127.0.0.1:8000/deleteUserAdmin?user_id='+id,
        {
            method: 'PUT',
        })
        .then( resp => {
            console.log(resp);
            return resp.json()
        } )
        .then( data => {

            if (data['status'] === 200) {
                var r = confirm("Gebruiker verwijdered!");
                if (r === true){
                    window.location.reload();
                } else {
                    window.location.reload();
                }
            } else if (data['status'] === 205) {

                window.location.href = "/";
            } else {
                alert("Gebruikersnaam of wachtwoord is incorrect.");
            }
        })
}

function isPositiveInteger(str) {
    if (typeof str !== 'string') {
        return false;
    }

    const num = Number(str);

    return Number.isInteger(num) && num > 0;
}