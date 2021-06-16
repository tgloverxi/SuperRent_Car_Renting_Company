<html>
<style>
    body {
        background-color: white;
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-position: center;
        background-size: cover;
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
        font-size: 20px;
        padding: 20px;
        display: block;
        width: 100px;
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
        width: 600px;
        padding: 10px;
        margin: 40px auto;
    }
    .fromBox {
        padding: 10px;
        margin: 50px auto;
    }
    .center {
        text-align: center
    }
</style>
<center><IMG SRC="WGM logo.png" ALT="" WIDTH=200 HEIGHT=200></center>
<body>

<div class="header">
    <h1>Rent with Reservation</h1>
</div>

  <div>
      <form action="rentWithReservation.php" method="POST">
    <fieldset class="box">
        <legend class="headerText"> Information: </legend>
        <p><label for="confirmation number">Confirmation Number: </label>
            <input type="text" id="confirmation_num" name="confirmation_num" placeholder="your confirmation number"></p>

        <p><label for="odometer"> Odometer: </label>
            <input type="text" id="odometer" name="odometer" placeholder="your car's odometer"></p>

        <p><label for="card type"> Card Type: </label>
            <select name="select_catalog" id="select_catalog">
                <option value="Master"> Master </option>
                <option value="Visa"> Visa </option>
            </select>
        </p>

        <p><label for="card number"> Card Number: </label>
            <input type="text" id="card_number" name="card_number" placeholder="your card number"></p>

        <p><label for="phoneNo."> Expire Date: </label>
            <input type="text" id="expire_date" name="expire_date" placeholder="your expire date"></p>
    </fieldset>

    <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
<!--    <input type="submit" name="insertSubmit" id = "insertSubmit">-->
          <button class="button" name="insertSubmit" id="insertSubmit" style="vertical-align: middle"><span>Rent</span></button>
</form>
<!--      <form action="rentWithReservation.php" method="GET">-->
<!--          <input type="hidden" id="receiptRequest" name="receiptRequest">-->
<!--          <input type="submit" value="receipt" name="receipt" ></p>-->
<!--      </form>-->
  </div>

<form action="clerk.php" method="get">
    <button class="button" style="vertical-align: middle"><span>Back</span></button>
</form>

<!--<div>-->
<!--    <h4 class = "center">Receipt</h4>-->
<!--    <form method="GET" action="rentWithReservation.php">-->
<!--        <table class = "center">-->
<!--            <tr>-->
<!--                <td><font size="2>Rent_ID</font></td>-->
<!--                <td><font size="2"></font></td>-->
<!--            </tr>-->
<!--            <tr>-->
<!--                <td><input type="text" name="purchaseEmail" size="20"></td>-->
<!--                <td><input type="text" name="purchaseGID" size="20"></td>-->
<!--            </tr>-->
<!--        </table>-->
<!--    </form>-->
<!--</div>-->

<?php
//this tells the system that it's no longer just parsing html; it's now parsing PHP

$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = NULL; // edit the login credentials in connectToDB()
$show_debug_alert_messages = False;// set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

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

function printResult($result) {//prints results from a select statement
    echo "<center><h4>Receipt</h4></center>";
    echo "<center><table></center>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
//        echo "<center><tr><td>" .'Rent_ID'. "</td><td>" . $row[0] . "</td></tr></center>";
        echo "<tr><td>" .'Confirmation_number'. "</td><td>" . $row[0] . "</td></tr>";
        echo "<tr><td>" .'Car_plate_number'. "</td><td>" . $row[1] . "</td></tr>";
        echo "<tr><td>" .'Driver_License'. "</td><td>" . $row[2] . "</td></tr>";
        echo "<tr><td>" .'Rent_From_Date'. "</td><td>" . $row[3] . "</td></tr>";
        echo "<tr><td>" .'Car_Type'. "</td><td>" . $row[4] . "</td></tr>";
        echo "<tr><td>" .'location'. "</td><td>" . $row[5] . "</td></tr>";
        echo "<tr><td>" .'City'. "</td><td>" . $row[6] . "</td></tr>"; //or just use "echo $row[0]"
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

//function handleUpdateRequest() {
//    global $db_conn;
//
//    $old_name = $_POST['oldName'];
//    $new_name = $_POST['newName'];
//
//    // you need the wrap the old name and new name values with single quotations
//    executePlainSQL("UPDATE demoTable SET name='" . $new_name . "' WHERE name='" . $old_name . "'");
//    OCICommit($db_conn);
//}
//
//function handleResetRequest() {
//    global $db_conn;
//    // Drop old table
//    executePlainSQL("DROP TABLE demoTable");
//
//    // Create new table
//    echo "<br> creating new table <br>";
//    executePlainSQL("CREATE TABLE demoTable (id int PRIMARY KEY, name char(30))");
//    OCICommit($db_conn);
//}
//
//function handleInsertRequest() {
//    global $db_conn;
//    $temp = $_POST['insNo'];
//
//
//    //Getting the values from user and insert data into the table
//    $tuple = array (
//        ":bind1" => $_POST['insNo'],
//        ":bind2" => $_POST['insName']
//    );
//
//    $alltuples = array (
//        $tuple
//    );
//
//    executeBoundSQL("insert into demoTable values (:bind1, :bind2)", $alltuples);
//    OCICommit($db_conn);
//}
//
//function handleCountRequest() {
//    global $db_conn;
//
//    $result = executePlainSQL("SELECT Count(*) FROM demoTable");
//
//    if (($row = oci_fetch_row($result)) != false) {
//        echo "<br> The number of tuples in demoTable: " . $row[0] . "<br>";
//    }
//}

function handleInsertRentalRequest () {
    global $db_conn;
    $conf = $_POST['confirmation_num'];
    $result = executePlainSQL("SELECT * FROM reservations WHERE reservations.confNo = '$conf'");
    $row = oci_fetch_row($result);

    if ($row != false) {

        $result2 = executePlainSQL("SELECT vlicense FROM reservations WHERE confNo= '$conf' ");
        $vlicense = "";
        if (($row = oci_fetch_row($result2)) != false) {
            $vlicense = $row[0];
        }
        $result0 = executePlainSQL("SELECT * FROM vehicles WHERE vlicense= '$vlicense' AND status <> 'rented' ");
        $row1 = oci_fetch_row($result0);

        if($row1 != false) {
            $result = executePlainSQL("SELECT confNo, vlicense, dlicense, fromTime, toTime FROM reservations WHERE reservations.confNo = '$conf' ");
            $vl = "a";
            $dl = "";
            $ft = "";
            $tt = "";
            while (($row = oci_fetch_row($result)) != false) {
                $vl = $row[1];
                $dl = $row[2];
                $ft = $row[3];
                $tt = $row[4];
            }
            //Getting the values from user and insert data into the table
            $rrid = rand(1, 2147483647);
            $result = executePlainSQL("SELECT * FROM rentals WHERE rid = '$rrid'");
            $row = oci_fetch_row($result);
            while ($row != false) {
                $rrid = rand(1, 2147483647);
                $result = executePlainSQL("SELECT * FROM rentals WHERE rid = '$rrid'");
                $row = oci_fetch_row($result);
            }
            $tuple = array(
                ":rid" => $rrid,
                ":vlicense" => $vl,
                ":dlicense" => $dl,
                ":fromTime" => $ft,
                ":toTime" => $tt,
                ":odometer" => $_POST['odometer'],
                ":cardName" => $_POST['select_catalog'],
                ":cardNo" => $_POST['card_number'],
                ":expDate" => $_POST['expire_date'],
                ":confNo" => $_POST['confirmation_num']
            );
            $alltuples = array(
                $tuple
            );

            executeBoundSQL("INSERT INTO rentals values (:rid, :vlicense, :dlicense, :fromTime, :toTime, :odometer, :cardName, :cardNo, :expDate, :confNo) ", $alltuples);
            executePlainSQL("UPDATE vehicles SET status = 'rented' WHERE vlicense = '$vlicense'");
            $result1 = executePlainSQL("SELECT confNo, vlicense, dlicense, CAST(fromTime AS DATE), vtName, location, city FROM reservations NATURAL INNER JOIN vehicles WHERE confNo = '$conf'");
            echo "<center><h4>Rent completed!</h4></center>";
            echo "<center><h4>Receipt</h4></center>";
            echo "<center><table></center>";

            while ($row = OCI_Fetch_Array($result1, OCI_BOTH)) {
        echo "<center><tr><td>" .'Rent_ID'. "</td><td>" . $rrid . "</td></tr></center>";
                echo "<tr><td>" .'Confirmation_number'. "</td><td>" . $row[0] . "</td></tr>";
                echo "<tr><td>" .'Car_plate_number'. "</td><td>" . $row[1] . "</td></tr>";
                echo "<tr><td>" .'Driver_License'. "</td><td>" . $row[2] . "</td></tr>";
                echo "<tr><td>" .'Rent_From_Date'. "</td><td>" . $row[3] . "</td></tr>";
                echo "<tr><td>" .'Car_Type'. "</td><td>" . $row[4] . "</td></tr>";
                echo "<tr><td>" .'location'. "</td><td>" . $row[5] . "</td></tr>";
                echo "<tr><td>" .'City'. "</td><td>" . $row[6] . "</td></tr>"; //or just use "echo $row[0]"
            }

            echo "</table>";
        } else {
            echo "<br> This car is already rented by someone! <br>";
        }
    } else {
        echo "<br> Invalid confirmation number. Please try again! <br>";
    }
    OCICommit($db_conn);

}
//
//function handlePrintRequest(){
//    global $db_conn;
//    $conf = $_SESSION["conf"];
//    $result = executePlainSQL("SELECT * FROM reservations WHERE reservations.confNo = '$conf'");
//    $row = oci_fetch_row($result);
//    if ($row != false) {
//        $result1 = executePlainSQL("SELECT rid, confNo, vlicense, dlicense, CAST(fromTime AS DATE), vtName, location, city FROM rentals NATURAL INNER JOIN vehicles WHERE confNo = '$conf'");
//        printResult($result1);
//    } else {
//        echo "<br> Invalid confirmation number. Please try again! <br>";
//    }
//    OCICommit($db_conn);
//}


// HANDLE ALL POST ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
function handlePOSTRequest() {
    if (connectToDB()) {
        if (array_key_exists('insertQueryRequest', $_POST)) {
            handleInsertRentalRequest ();

        }

        disconnectFromDB();
    }
}

// HANDLE ALL GET ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
//function handleGETRequest() {
//    if (connectToDB()) {
//        if (array_key_exists('receipt', $_GET)) {
//           handlePrintRequest();
//        }
//
//        disconnectFromDB();
//    }
//}

if (isset($_POST['insertSubmit'])) {
    handlePOSTRequest();
}
//} else if (isset($_GET['receiptRequest'])) {
//    handleGETRequest();
//}
?>
</body>
</html>