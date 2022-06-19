document.addEventListener("DOMContentLoaded", function() {
    document.querySelector('#submit').addEventListener('click', btn => {
        btn.preventDefault();
        let email = document.querySelector("#email");
        let username = document.querySelector("#username");
        let password = document.querySelector("#password");

        fetch('http://127.0.0.1:8000/registerUser?email='+email.value+'&username='+username.value+'&password='+password.value+'',
            {
                method: 'POST',
            })
            //.then( resp => resp.json() )
            .then( resp => console.log(resp) )
    });
});