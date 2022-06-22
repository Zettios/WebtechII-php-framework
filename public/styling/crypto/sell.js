document.addEventListener("DOMContentLoaded", function() {
    document.querySelector('#sellButton').addEventListener('click', btn => {
        btn.preventDefault();
        let amountToBuy = document.querySelector("#amountToSell").value;
        let url = window.location.href;
        let cryptoId = url.split('/').at(-1);

        fetch('http://127.0.0.1:8000/soldCrypto?crypto_id='+cryptoId+'&buy_amount='+amountToBuy+'',
            {
                method: 'PUT',
            })
            .then( resp => resp.json() )
            .then( data => {
                if (data['status'] === 200) {
                    document.location.href = window.location.href;
                    alert('Crypto sold!')
                } else {
                    alert("Failed to add. Check your balance if it's enough.");
                }
            })
    });
});