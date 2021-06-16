<?php
session_save_path('/home/y/yuxinwan/public_html');
session_start();
?>
<html>
    <head>
        <title>CPSC 304 PHP/Oracle Demonstration</title>
    </head>

    <style>
        body {
            background-image: url("img.jpg");
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: center;
            background-size: cover;
        }
        .button {
            border-radius: 8px;
            background-color: hsla(30,100%,50%,0.5);
            border: none;
            color: #FFFFFF;
            text-align: center;
            font-size: 28px;
            padding: 20px;
            display: block;
            width: 200px;
            transition: all 0.5s;
            cursor: pointer;
            margin: 50px auto;
        }
        .button span {
            cursor: pointer;
            display: inline-block;
            position: relative;
            transition: 0.5s;
        }
        .button span:after {
            content: '\00bb';
            position: absolute;
            opacity: 0;
            top: 0;
            right: -20px;
            transition: 0.5s;
        }
        .button:hover span {
            padding-right: 25px;
        }

        .button:hover span:after {
            opacity: 1;
            right: 0;q1
        }
        div {
            border-radius: 5px;
            background-color: #f2f2f2;
            padding: 20px;
            width: 60%;
            margin: 30px auto;
        }
    </style>
    <body>
    <div>
        <legend>Hi, customer here are three options we can make:</legend>
        <label> <?php echo $_SESSION["C_license"]; ?> <label>
        <form action="makeReservation.php" method="get">
            <button class="button" style="vertical-align:middle"><span>Make Reservation</span></button>
        </form>

        <form action="cancelReservation.php" method="get">
            <button class="button" style="vertical-align:middle"><span>Cancel Reservation</span></button>
        </form>
        <form action="startPage.php" method="get">
            <input type="hidden" id="signOut" name="signOut">
            <button class="button" name = "sign_out" style="vertical-align:middle"><span>Sign out</span></button>
        </form>

    </div>
    <form action="startPage.php" method="get">
        <button class="button" style="vertical-align: middle"><span>Back</span></button>
    </form>
        <?php
        $success = True; //keep track of errors so it redirects the page only if there are no errors

        function signOutRequest() {
            $_SESSION["C_dlicense"] = "";
        }

        function handleCancelRequest() {
            if (array_key_exists('signOut', $_GET)) {
                signOutRequest();
            }
        }

        if (isset($_GET['sign_out'])) {
            handleSignOutRequest();
        }
//		?>
	</body>
</html>

