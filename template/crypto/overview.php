extend(base-access.php)
<br>
blockstart(head)
<title>User - BitTraders</title>
<link rel="stylesheet" href="../styling/overview/overview.css">
<script type="text/javascript" src="../styling/overview/overview.js"></script>
blockend(head)
blockstart(body)
<h1 id="title">Cryptos</h1>
forloopstart(crypto)
<div class="cryptobar">
    <span>forarg(name)</span>: <span>forarg(value)</span>
    <input type="button" onclick="location.href='/crypto/forarg(crypto_id)';" value="Buy forarg(name)" />
    <input type="button" onclick="location.href='/crypto/forarg(crypto_id)';" value="Sell forarg(name)" />
</div>
forloopend(crypto)
<?php echo "p" ?>
blockend(body)

blockstart(footer)
blockend(footer)