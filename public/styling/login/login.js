document.addEventListener("DOMContentLoaded", function() {
    console.log("I have been made in template/js! - Login");

    document.querySelector('#submit').addEventListener('click', btn => {
        btn.preventDefault();
        console.log('Pressed!');
    });
});