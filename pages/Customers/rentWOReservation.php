<?php
session_save_path('/home/y/yuxinwan/public_html');
session_start();
?>

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
        font-size: 28px;
        padding: 20px 20px;
        display: inline-block;
        width: 200px;
        transition: all 0.5s;
        cursor: pointer;
        margin: 10px 10px;
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
    input[type=text], select {
        width: 30%;
        padding: 12px 20px;
        margin: 8px 0;
        display: inline-block;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }
    input[type=submit] {
        width: 600px;
        position: center;
        background-color: hsla(30,100%,50%,0.5);
        color: white;
        padding: 14px 20px;
        margin: 8px 0;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    input[type=submit]:hover {
        background-color: hsla(30, 100%, 50%, 0.65);
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
    .left-container {
        float: left;
    }
    .right-container {
        float: right;
    }
</style>

<body>

<div class="header">
    <h1>Rent Without Reservation</h1>
</div>

<div class="right-container">
    <form action = "rentWOReservation.php" method = "GET">
        <fieldset class="box">
            <legend class="headerText">Total Available Result: </legend>
            <input type="hidden" id="showDetail" name="showDetail">
            <label>Number of Results:</label>
            <input type="submit" name="numOfResult" value= "see details" >
        </fieldset>
    </form>
    <form action="rentWOReservation.php" method="POST">
        <fieldset class="box">
            <legend class="headerText">Complete your reservation: </legend>
            <br>Enter the vlicense of the vehicle you want to choose: </br>
            <input type="text" name="vlicense" placeholder="enter the vlicense">
            <br>Enter the customer driver license: </br>
            <input type="text" name="dlicense" placeholder="enter the driver license">
            <br>Enter the vlicense's odometer: </br>
            <input type="text" name="odometer" placeholder="enter the car's odometer">
            <p><label for="card type"> Card Type: </label>
                <select name="select_catalog" id="select_catalog">
                    <option value="Master"> Master </option>
                    <option value="Visa"> Visa </option>
                </select>
            </p>
            <br>Enter the customer's card number: </br>
            <input type="text" name="cardnum" placeholder="enter the card number">
            <br>Enter the card expire date: </br>
            <input type="text" name="expdate" placeholder="enter the card's expire date">
            <input type="hidden" id="reserve" name="reserve">
            <input type="submit" name="submit_reservation" value = "Rent" style="horiz-align: left">
        </fieldset>
    </form>
    <form action="clerk.php" method="get">
        <button class="button" style="horiz-align: right"><span>Back</span></button>
    </form>
</div>

<div class="left-container">
    <form action = "rentWOReservation.php" method="POST">
        <fieldset class="box">
            <legend class="headerText">Car Type:</legend>
            <input name="carType[]" id = "carType" type="checkbox" value="SUV" /> SUV <br />
            <input name="carType[]" id = "carType" type="checkbox" value="compact" /> compact <br />
            <input name="carType[]" id = "carType" type="checkbox" value="full-size" /> full-size <br />
            <input name="carType[]" id = "carType" type="checkbox" value="standard" /> standard <br />
            <input name="carType[]" id = "carType" type="checkbox" value="truck" /> truck <br />
            <input name="carType[]" id = "carType" type="checkbox" value="economy" /> economy <br />
            <input name="carType[]" id = "carType" type="checkbox" value="min-size" /> min-size <br />
        </fieldset>

        <fieldset class="box">
            <legend class="headerText">City:</legend>
            <input name="city" type="radio" value="The Great Vancouver"/> The Great Vancouver <br />
            <input name="city" type="radio" value="Toronto" /> Toronto <br />
        </fieldset>

        <fieldset class="box">
            <legend class="headerText">Location:</legend>
            <label for="From">The Great Vancouver:</label><br />
            <div class="fromBox">
                <select name = "Van_loc[]" multiple>
                    <option value="downtown"> downtown </option>
                    <option value="UBC"> UBC </option>
                    <option value="SFU"> SFU </option>
                    <option value="granville"> granville </option>
                </select>
            </div>
            <label for="From">Toronto:</label><br/>
            <div class="fromBox">
                <select name = "T_loc[]" multiple>
                    <option value="UT"> UT </option>
                    <option value="downTown"> downtown </option>
                </select>
            </div>
        </fieldset>

        <fieldset class="box">
            <legend class="headerText">Booking Time:</legend>
            <label for="From">From:</label><br />
            <div class="fromBox">
                Start Time (date and time): <input type="datetime-local" name="startTime">
            </div>
            <label for="From">To:</label><br />
            <div class="fromBox">
                End Time (date and time): <input type="datetime-local" name="EndTime">
            </div>
        </fieldset>

        <input type="hidden" id="searchRequest" name="searchRequest">
        <input type="submit" id="search" name="search">
    </form>
</div>

<?php
//this tells the system that it's no longer just parsing html; it's now parsing PHP
// session_save_path('/home/y/yuxinwan/public_html');
// session_start();

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
//    echo "<br>running ".$cmdstr."<br>";
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

function printResult() { //prints results from a select statement
    if ($_SESSION['resNo'] == 0) {
        echo "<br>No available Vehicles found! No more details...<br>";
    } else {
        echo "<br>finally got: " . $_SESSION['resNo'] . " vehicles!<br>";
        echo "<center><table></center>";
        echo "<tr><th>vlicense</th><th>make</th><th>model</th><th>year</th><th>color</th><th>odometer</th><th>vtName</th><th>location</th><th>city</th></tr>";
        foreach ($_SESSION['availableVehicles'] as $row) {
            echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] .
                "</td><td>" . $row[4] . "</td><td>" . $row[5] . "</td><td>" . $row[6] . "</td><td>" . $row[7] . "</td><td>" . $row[8] . "</td></tr>"; //or just use "echo $row[0]"
        }
        echo "</table>";
    }
    // echo "<tr><td>" . $row["vlicense"] . "</td><td>" . $row["make"] . "</td><td>" . $row["model"] . "</td><td>" . $row["year"] .
    //         "</td><td>" . $row["color"] . "</td><td>" . $row["odometer"] . "</td><td>" . $row["vtName"] . "</td><td>" . $row["location"] . "</td><td>" . $row["city"] . "</td></tr>";

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

function searchInRequest() {
    global $db_conn;
    $vtype = array();
    $query = "SELECT * FROM vehicles WHERE status <> 'rented' AND status <> 'reserved'";
    $city = "";
    $locations = array();
    $_SESSION["from_time"] = date('Y-m-d h:i', strtotime($_POST["startTime"]));
    $_SESSION["to_time"] = date('Y-m-d h:i', strtotime($_POST["EndTime"]));

    if(!empty($_POST['carType'])) {
        foreach($_POST['carType'] as $check) {
            array_push($vtype, $check);
        }
    }
    $strType = "";

    for ($j = 0; $j < sizeof($vtype); $j++) {
        if ($j == sizeof($vtype) - 1) {
            $strType = $strType . "vtName = '$vtype[$j]'";
        } else {
            $strType = $strType . "vtName = '$vtype[$j]' OR ";
        }
    }

    if ($_POST["city"] == "The Great Vancouver") {
        $city = "GT Van";
        $locations = $_POST['Van_loc'];
    } else if ($_POST["city"] == "Toronto") {
        $city = "Toronto";
        $locations = $_POST['T_loc'];
    }

    if ($city == "GT Van" || $city == "Toronto") {
        $query = $query . " AND city = '$city'";
        if (sizeof($locations) != 0) {
            $query = $query . " AND (";
            for ($i = 0; $i < sizeof($locations); $i++) {
                if ($i == sizeof($locations) - 1) {
                    $query = $query . "location = '$locations[$i]'";
                } else {
                    $query = $query . "location = '$locations[$i]' OR ";
                }
            }
            $query = $query . ")";
        }
        if (!empty($vtype)) {
            $query = $query . " AND (" . $strType . ")";
        }
    } else {
        if (!empty($vtype)) {
            $query = $query . " AND ( " . $strType . ")";
        }
    }

    $availableVehicles = executePlainSQL($query);
    $_SESSION['resNo'] = 0;
    $_SESSION['availableVehicles'] = array();
    while (($row = oci_fetch_row($availableVehicles)) != false) {
        $_SESSION['resNo'] ++;
        array_push($_SESSION['availableVehicles'], $row);
    }
    if ($_SESSION['resNo'] == 0) {
        echo "<br>Sorry! There are no available Vehicles with your requirements!<br>";
    } else {
        echo "<br>finally got: " . $_SESSION['resNo'] . " vehicles!<br>";
    }
}

function insertReserveRequest() {
    global $db_conn;
    $confNo = rand(0, 100000000);
    $result_0 = executePlainSQL("SELECT * FROM rentals WHERE rid = '$confNo'");
    $row_0 = oci_fetch_row($result_0);
    while($row_0 != false){
        $confNo = rand(0, 100000000);
        $result = executePlainSQL("SELECT * FROM rentals WHERE rid = '$confNo'");
        $row_0 = oci_fetch_row($result);
    }
    $vId = $_POST['vlicense'];
    //Getting the values from user and insert data into the table

//    $cid = $_SESSION["C_dlicense"];
    $cid = $_POST['dlicense'];
    $result5 = executePlainSQL("SELECT vtName, location, city FROM vehicles WHERE vlicense = '$vId'");
    //SELECT vtName, location FROM vehicles WHERE vlicense = 'KO8818';
    $row = oci_fetch_row($result5);
//    $vt = "";
//    $loc = "";
//    while (($row = oci_fetch_row($result5)) != false) {
//        $vt = $row[0];
//        $loc = $row[1];
//        echo "<br> ??????$vt <br>";
//        echo "<br> ??????$loc <br>";
//    }

    $result0 = executePlainSQL("SELECT * FROM customers WHERE dlicense = '$cid'");
    $row1 = oci_fetch_row($result0);
    if ($row1 != false) {
        $ft = $_SESSION["from_time"];
        $tt = $_SESSION["to_time"];
        executePlainSQL("INSERT INTO reservations VALUES ('$confNo', '$row[0]', '$vId', '$cid', TO_DATE('$ft', 'yyyy-mm-dd hh24:mi'), TO_DATE('$tt', 'yyyy-mm-dd hh24:mi'))");
        executePlainSQL("UPDATE vehicles SET status = 'rented' WHERE vlicense = '$vId'");
        $rrid = rand(1, 2147483647);
        $result4 = executePlainSQL("SELECT * FROM rentals WHERE rid = '$rrid'");
        $row2 = oci_fetch_row($result4);
        while ($row2 != false) {
            $rrid = rand(1, 2147483647);
            $result4 = executePlainSQL("SELECT * FROM rentals WHERE rid = '$rrid'");
            $row2 = oci_fetch_row($result4);
        }

        $tuple = array(
            ":rid" => $rrid,
            ":vlicense" => $vId,
            ":dlicense" => $cid,
            ":fromTime" => $ft,
            ":toTime" => $tt,
            ":odometer" => $_POST['odometer'],
            ":cardName" => $_POST['select_catalog'],
            ":cardNo" => $_POST['cardnum'],
            ":expDate" => $_POST['expdate'],
            ":confNo" => $_POST['$confNo']
        );
        $alltuples = array(
            $tuple
        );

        executeBoundSQL("INSERT INTO rentals values (:rid, :vlicense, :dlicense, TO_DATE('$ft', 'yyyy-mm-dd hh24:mi'), TO_DATE('$tt', 'yyyy-mm-dd hh24:mi'), :odometer, :cardName, :cardNo, :expDate, :confNo) ", $alltuples);

        //echo "<center><h4>Reservation Receipt</h4></center>";
        echo "<center><h4>Rent completed!</h4></center>";
        echo "<center><table></center>";
        echo "<tr><th><h2>" . 'Rent' . "<h2></th><th><h2>" . "Receipt" . "<h2></th></tr>";
        echo "<tr><td>" . 'RentId' . "</td><td>" . $rrid . "</td></tr>";
        echo "<tr><td>" . 'Confirmation_number' . "</td><td>" . $confNo . "</td></tr>";
        echo "<tr><td>" . 'Car_type' . "</td><td>" . $row[0] . "</td></tr>";
        echo "<tr><td>" . 'Car_plate_number' . "</td><td>" . $vId . "</td></tr>";
        echo "<tr><td>" . 'Driver_License' . "</td><td>" . $cid . "</td></tr>";
        echo "<tr><td>" . 'Rent_From_Date' . "</td><td>" . $ft . "</td></tr>";
        echo "<tr><td>" . 'Rent_to_Date' . "</td><td>" . $tt . "</td></tr>";
        echo "<tr><td>" . 'location' . "</td><td>" . $row[1] . "</td></tr>";
        echo "<tr><td>" . 'city' . "</td><td>" . $row[2] . "</td></tr>";
        echo "</table>";

    } else {
        echo "<br> The customer does not exists in the system, need to sign up first <br>";
    }
    OCICommit($db_conn);
}

// HANDLE ALL GET ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
function handleSearchRequest() {
    if (connectToDB()) {
        if (array_key_exists('searchRequest', $_POST)) {
            searchInRequest();
        } else if (array_key_exists('reserve', $_POST)) {
            insertReserveRequest();
        }
        disconnectFromDB();
    }
}

function handleDetailRequest() {
    if (connectToDB()) {
        if (array_key_exists('showDetail', $_GET)) {
            printResult();
        }
        disconnectFromDB();
    }
}

if (isset($_POST['submit_reservation']) || isset($_POST['search'])) {
    handleSearchRequest();
} else if (isset($_GET['numOfResult'])) {
    handleDetailRequest();
}
?>
</body>
</html>

