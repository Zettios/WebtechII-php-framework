document.addEventListener("DOMContentLoaded", function() {
    document.querySelector('#submit').addEventListener('click', btn => {
        btn.preventDefault();
        let username = document.querySelector("#username");
        let email = document.querySelector("#email");
        let password = document.querySelector("#password");

        console.log(username.value);

        fetch('http://127.0.0.1:8000/updateUser?username='+username.value+'&email='+email.value+'&password='+password.value+'',
            {
                method: 'PUT',
            })
            .then( resp => resp.json() )
            .then( data => {
                if (data['status'] === 200) {
                    alert("Gegevens geupdate!");
                } else if (data['status'] === 403){
                    alert("Gebruikersnaam bestaat al.");
                } else {
                    alert("Gebruikersnaam of wachtwoord is incorrect.");
                }
            })
    });
});