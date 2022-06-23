document.addEventListener("DOMContentLoaded", function() {
    var bars = document.getElementsByClassName('editfield');
    var buttons = document.getElementsByClassName('save');

    for(var i = 0; i < bars.length; i++) {
        bars[i].id = i;
    }

    for(var j = 0; j < buttons.length; j++) {
        buttons[j].id = j;
        buttons[j].addEventListener('click', btn => {
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
                            }
                        } else if (data['status'] === 403){
                            alert("Gebruikersnaam bestaat al.");
                        } else {
                            alert("Gebruikersnaam of wachtwoord is incorrect.");
                        }
                    })
            }
        });
    }
});

function isPositiveInteger(str) {
    if (typeof str !== 'string') {
        return false;
    }

    const num = Number(str);

    return Number.isInteger(num) && num > 0;
}