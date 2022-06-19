document.addEventListener("DOMContentLoaded", function() {
    document.querySelector('#submit').addEventListener('click', btn => {
        btn.preventDefault();
        let email = document.querySelector("#email");
        let username = document.querySelector("#username");
        let password = document.querySelector("#password");

        fetch('http://127.0.0.1:8000/api/login_check',
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json;charset=utf-8'
                },
                body: '{"email":"'+email+'", "username":"'+username+'","password":"'+password+'"}'
            })
            .then( resp => console.log(resp) )
    });
});