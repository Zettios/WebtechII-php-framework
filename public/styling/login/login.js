document.addEventListener("DOMContentLoaded", function() {
    document.querySelector('#submit').addEventListener('click', btn => {
        btn.preventDefault();
        let username = document.querySelector("#username");
        let password = document.querySelector("#password");

        fetch('http://127.0.0.1:8000/loginUser?username='+username.value+'&password='+password.value+'',
            {
                method: 'GET',
            })
            //.then( resp => resp.json() )
            .then( resp => resp.json() )
            .then( data => {
                if (data['status'] === 200) {
                    document.location.href = '/user';
                }
            })
    });
});