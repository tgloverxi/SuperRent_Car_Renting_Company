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
        background-color: white;
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-position: center;
        background-size: cover;
    }
    .textSize {
        width: 200px;
        height: 40px;
        font-size: 20px;
    }
    .header {
        background-color: whitesmoke;
        padding: 20px;
        text-align: center;
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
        right: 0;
    }
    .headerText {
        background-color: hsla(30,100%,50%,0.5);
        color: whitesmoke;
        letter-spacing: 3px;
    }
    .box {
        background-color: whitesmoke;
        width: 400px;
        padding: 10px;
        height: 300px;
        margin: 40px auto;
    }
    .button-box {
        width: 420px;
        padding: 10px;
        margin: 5px auto;
    }
    .fromBox {
        padding: 10px;
        margin: 0px auto;
    }
</style>
<center><IMG SRC="WGM logo.png" ALT="" WIDTH=200 HEIGHT=200></center>
<body>

<div class="header">
    <h1>Cancel Reservation</h1>
</div>


<form action="cancelReservation.php" method="POST" accept-charset="UTF-8" autocomplete="off">
    <fieldset class="box">
        <legend class="headerText">Your Reservation Record: </legend>
        Confirmation number: <input class="textSize" type="text" name="reserveNo"> <br />
        <input type="hidden" id="cancelReservation" name="cancelReservation">
        <button class="button" name = "cancel" style="vertical-align:middle"><span>Confirm</span></button>
    </fieldset>
</form>
<form action="cancelReservation.php" method="GET" accept-charset="UTF-8" autocomplete="off">
    <input type="hidden" id="seeReservations" name="seeReservations">
    <button class="button" name = "display" style="vertical-align:middle"><span>View My Reservations</span></button>
</form>

<div class="button-box">
    <form action="customer.php" method="get">
        <button class="button" style="vertical-align: middle"><span>Back</span></button>
    </form>
</div>


<!--<form action="c_signin.php" method="get">-->
<!--    <button class="button" style="vertical-align:middle"><span>Customer Sign in</span></button>-->
<!--</form>-->
<!---->
<!--<form action="cle_signin,php" method="get">-->
<!--    <button class="button" style="vertical-align:middle"><span>Clerk Sign in</span></button>-->
<!--</form>-->
<!---->
<!--<form action="" method="get">-->
<!--    <button class="button" style="vertical-align:middle"><span>Sign up</span></button>-->
<!--</form>-->

<?php
        //this tells the system that it's no longer just parsing html; it's now parsing PHP

        $success = True; //keep track of errors so it redirects the page only if there are no errors
        $db_conn = NULL; // edit the login credentials in connectToDB()
        $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

        function debugAlertMessage($message) {
            global $show_debug_alert_messages;

            if ($show_debug_alert_messages) {
                echo "<script type='text/javascript'>alert('" . $message . "');</script>";
            }
        }

        function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
            //echo "<br>running ".$cmdstr."<br>";
            global $db_conn, $success;

            $statement = OCIParse($db_conn, $cmdstr); 
            //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
                echo htmlentities($e['message']);
                $success = False;
            }

            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
                echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
                echo htmlentities($e['message']);
                $success = False;
            }

            return $statement;
        }

        function executeBoundSQL($cmdstr, $list) {
            /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
        In this case you don't need to create the statement several times. Bound variables cause a statement to only be
        parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection. 
        See the sample code below for how this function is used */

            global $db_conn, $success;
            $statement = OCIParse($db_conn, $cmdstr);

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn);
                echo htmlentities($e['message']);
                $success = False;
            }

            foreach ($list as $tuple) {
                foreach ($tuple as $bind => $val) {
                    //echo $val;
                    //echo "<br>".$bind."<br>";
                    OCIBindByName($statement, $bind, $val);
                    unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
                }

                $r = OCIExecute($statement, OCI_DEFAULT);
                if (!$r) {
                    echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                    $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
                    echo htmlentities($e['message']);
                    echo "<br>";
                    $success = False;
                }
            }
        }

        function printResult($result) { //prints results from a select statement
            echo "<br>Retrieved data from table demoTable:<br>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row["NID"] . "</td><td>" . $row["NAME"] . "</td></tr>"; //or just use "echo $row[0]" 
            }

            echo "</table>";
        }

        function connectToDB() {
            global $db_conn;

            // Your username is ora_(CWL_ID) and the password is a(student number). For example, 
            // ora_platypus is the username and a12345678 is the password.
            $db_conn = OCILogon("ora_yuxinwan", "a23838436", "dbhost.students.cs.ubc.ca:1522/stu");

            if ($db_conn) {
                debugAlertMessage("Database is Connected");
                return true;
            } else {
                debugAlertMessage("Cannot connect to Database");
                $e = OCI_Error(); // For OCILogon errors pass no handle
                echo htmlentities($e['message']);
                return false;
            }
        }

        function disconnectFromDB() {
            global $db_conn;

            debugAlertMessage("Disconnect from Database");
            OCILogoff($db_conn);
        }

        function cancelRequest() {
            global $db_conn;

            $record = $_POST['reserveNo'];
            $result = executePlainSQL("SELECT * FROM reservations WHERE confNo = '$record'");
            $row = oci_fetch_row($result);
            $vl = $_SESSION["V_license"];

            if ($row != false) {
                executePlainSQL("DELETE FROM reservations where confNo = '$record'");
                executePlainSQL("UPDATE vehicles SET status = 'available' where vlicense = '$vl'");
                 echo "<br>Your reservation: " . $record . " has been deleted!<br>";
            } else {
                echo "<br>Invalid confirmation number. Please enter again! <br>";
            }

            // you need the wrap the old name and new name values with single quotations
            OCICommit($db_conn);
        }


        function viewRequest() {
            global $db_conn;

            $cid = $_SESSION["C_dlicense"];
            echo "<br>Customer: " . $cid . "<br>";
            $result = executePlainSQL("SELECT confNo, vtName, vlicense, dlicense, CAST(fromTime AS DATE), CAST(toTime AS DATE) FROM reservations NATURAL JOIN vehicles WHERE status = 'reserved' AND dlicense = '$cid'");
            echo "<center><h4>Your current reservations</h4></center>";
            echo "<center><table></center>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                $_SESSION["V_license"] = $row[2];
//        echo "<center><tr><td>" .'Rent_ID'. "</td><td>" . $row[0] . "</td></tr></center>";
                echo "<tr><td>" .'Confirmation_number'. "</td><td>" . $row[0] . "</td></tr>";
                echo "<tr><td>" .'Car_Type'. "</td><td>" . $row[1] . "</td></tr>";
                echo "<tr><td>" .'Vehicle_License'. "</td><td>" . $row[2] . "</td></tr>";
                echo "<tr><td>" .'Driver_License'. "</td><td>" . $row[3] . "</td></tr>";
                echo "<tr><td>" .'Start_time'. "</td><td>" . $row[4] . "</td></tr>";
                echo "<tr><td>" .'End_time'. "</td><td>" . $row[5] . "</td></tr>";
            }

            echo "</table>";
        }

        // HANDLE ALL POST ROUTES
    // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleCancelRequest() {
            if (connectToDB()) {
                if (array_key_exists('cancelReservation', $_POST)) {
                    cancelRequest();
                }

                disconnectFromDB();
            }
        }

        function handleDisplayRequest() {
            if (connectToDB()) {
                if (array_key_exists('seeReservations', $_GET)) {
                    viewRequest();
                }

                disconnectFromDB();
            }
        }
        

        if (isset($_POST['cancel'])) {
            handleCancelRequest();
        } else if (isset($_GET['display'])) {
            handleDisplayRequest();
        }
        ?>
</body>
</html>

