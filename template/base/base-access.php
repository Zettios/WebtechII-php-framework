<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../styling/navbar.css">
    blockstart(head)
    blockend(head)
</head>
<body>
<div id="navbar"> <ul>
    <li><a href="#">Home</a></li>
    <li><a href="/user">Accountuu</a></li>
    <li><a href="/">Loguit</a></li>
    <?php
        if("arg(role)" === "2") {
            echo "p";
        } else {
            echo 'd';
        }
    ?>
</ul> </div>
<div id="container">
    blockstart(body)
    blockend(body)
</div>
<div id="footer">
    blockstart(footer)
    blockend(footer)
</div>
</body>
</html>