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
</style>
<center><IMG SRC="WGM logo.png" ALT="" WIDTH=200 HEIGHT=200></center>
<body>

<div class="header">
    <h1>Return Car</h1>
</div>

<div>
    <form action="returnCar.php" method="POST">
    <fieldset class="box">
        <legend class="headerText"> Information: </legend>
        <p><label for="Rental ID"> Rental ID: </label>
            <input type="text" id="Rental_ID" name="Rental_ID" placeholder="your rental id"></p>
        <p><label for="Return Time"> Return Time: </label>
            <input type="datetime-local" id="Return_Time" name="Return_Time" placeholder="your return time"></p>
        <p><label for="odometer"> Odometer: </label>
            <input type="text" id="odometer" name="odometer" placeholder="your car's odometer"></p>
        <p><label for="full stank"> Full Stank: </label>
            <input type="text" id="full_stank" name="full_stank" placeholder="is your stack full or not"></p>
    </fieldset>
        <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
        <!--    <input type="submit" name="insertSubmit" id = "insertSubmit">-->
        <button class="button" name="insertSubmit" id="insertSubmit" style="vertical-align: middle"><span>Return</span></button>
    </form>
</div>

<form action="clerk.php" method="GET">
    <button class="button" style="vertical-align: middle"><span>Back</span></button>
</form>

<?php
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

//function printResult($result) {//prints results from a select statement
//    echo "<center><h4>Receipt</h4></center>";
//    echo "<center><table id=\"cart\"></center>";
//
//    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
//        echo "<center><tr><td>" .'Rent_ID'. "</td><td>" . $row[0] . "</td></tr></center>";
//        echo "<tr><td>" .'Confirmation_number'. "</td><td>" . $row[1] . "</td></tr>";
//        echo "<tr><td>" .'Car_plate_number'. "</td><td>" . $row[2] . "</td></tr>";
//        echo "<tr><td>" .'Driver_License'. "</td><td>" . $row[3] . "</td></tr>";
//        echo "<tr><td>" .'Rent_From_Date'. "</td><td>" . $row[4] . "</td></tr>";
//        echo "<tr><td>" .'Car_Type'. "</td><td>" . $row[5] . "</td></tr>";
//        echo "<tr><td>" .'location'. "</td><td>" . $row[6] . "</td></tr>";
//        echo "<tr><td>" .'City'. "</td><td>" . $row[7] . "</td></tr>"; //or just use "echo $row[0]"
//    }
//
//    echo "</table>";
//}


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

function handleInsertRentalRequest () {
    global $db_conn;
    $rrid = $_POST['Rental_ID'];
    $result = executePlainSQL("SELECT * FROM rentals WHERE rid = '$rrid'");
    $row = oci_fetch_row($result);

    if ($row != false) {
        $result0 =  executePlainSQL("SELECT * FROM returns WHERE rid ='$rrid'");
        $row1 = oci_fetch_row($result0);
        if($row1 == false) {//Getting the values from user and insert data into the table
            $result1 = executePlainSQL("SELECT vehicleTypes.drate, vehicleTypes.dirate, CAST(rentals.fromTime AS DATE) FROM rentals, vehicles, vehicleTypes WHERE rentals.vlicense = vehicles.vlicense AND vehicles.vtName = vehicleTypes.vtName AND rentals.rid ='$rrid'");
            //"SELECT (CAST(rentals.toTime AS DATE)-CAST(rentals.fromTime AS DATE))*vehicleTypes.drate, (CAST(rentals.toTime AS DATE)-CAST(rentals.fromTime AS DATE))*vehicleTypes.dirate, (CAST(rentals.toTime AS DATE)-CAST(rentals.fromTime AS DATE))*vehicleTypes.drate + (CAST(rentals.toTime AS DATE)-CAST(rentals.fromTime AS DATE))*vehicleTypes.dirate FROM rentals, vehicles, vehicleTypes WHERE rentals.vlicense = vehicles.vlicense AND vehicles.vtName = vehicleTypes.vtName AND rentals.rid ='$rrid'");
            //
            $fromTime = "";
            $pricerate = "";
            $insurancerate = "";
            while (($row = oci_fetch_row($result1)) != false) {
                $pricerate = $row[0];
                $insurancerate = $row[1];
                $fromTime = $row[2];
            }
            $ret = date('d-M-y', strtotime($_POST['Return_Time']));
            $retime = date('Y-m-d h:i', strtotime($_POST['Return_Time']));

            $interval = $ret - $fromTime;
            $fee = ($ret - $fromTime) * $pricerate;
            $insurance = ($ret - $fromTime) * $insurancerate;
            $total = $fee + $insurance;
            $tuple = array(
                ":rid" => $rrid,
                ":retime" => date('Y-m-d h:i', strtotime($_POST['Return_Time'])),
                ":odometer" => $_POST['odometer'],
                ":fulltank" => $_POST['full_stank'],
                ":value" => $total
            );
            $alltuples = array(
                $tuple
            );
            executeBoundSQL("INSERT INTO returns values (:rid, TO_DATE('$retime', 'yyyy-mm-dd hh24:mi'), :odometer, :fulltank, :value ) ", $alltuples);
            $result3 = executePlainSQL("SELECT toTime FROM rentals WHERE rid = '$rrid' ");
            echo "<center><h4>Receipt</h4></center>";
            echo "<center><table id=\"cart\"></center>";

            while ($row = OCI_Fetch_Array($result3, OCI_BOTH)) {
                echo "<center><tr><td>" . 'Return_ID' . "</td><td>" . $rrid . "</td></tr></center>";
                echo "<tr><td>" . 'date of return' . "</td><td>" . $ret . "</td></tr>";
                echo "<tr><td>" . 'usage fee' . "</td><td>" . $fee . "</td></tr>";
                echo "<tr><td>" . 'insurance fee' . "</td><td>" . $insurance . "</td></tr>";
                echo "<tr><td>" . 'total cost' . "</td><td>" . $total . "</td></tr>";
            }
            echo "</table>";
            echo "<br> Total cost = usage fee + insurance fee <br>";
            echo "<br> Usage fee = daily rate * total days used <br>";
            echo "<br> Insurance fee = daily insurance rate * total day used <br>";
            $result2 = executePlainSQL("SELECT vlicense FROM rentals WHERE rid = '$rrid' ");
            $vlicense = "";
            if (($row = oci_fetch_row($result2)) != false) {
                $vlicense = $row[0];
            }
            executePlainSQL("UPDATE vehicles SET status = 'available' WHERE vlicense = '$vlicense'");
        } else {
            echo "<br> Already return this car! <br>";
        }
    } else {
        echo "<br> Invalid rental id. Please re-enter! <br>";
    }
    OCICommit($db_conn);

}


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
//        if (array_key_exists('countTuples', $_GET)) {
//
//        }
//
//        disconnectFromDB();
//    }
//}

if (isset($_POST['insertSubmit'])) {
    handlePOSTRequest();
}
//?>
</body>
</html>

