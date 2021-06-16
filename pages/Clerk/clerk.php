<html>
<style>
    body {
        background-image: url("img.jpg");
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-position: center;
        background-size: cover;
    }
    .header {
        background-color: whitesmoke;
        padding: 20px;
        text-align: center;
        opacity: 0.85;
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
        width: 800px;
        transition: all 0.5s;
        cursor: pointer;
        margin: 10px auto;
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
        letter-spacing: 1px;
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

<body>
<div class="header">
    <h1>Clerk</h1>
</div>

<div>
    <form action="rentWithReservation.php" method="get">
        <button class="button" style="vertical-align:middle"><span>Rent With Reservation</span></button>
    </form>

    <form action="rentWOReservation.php" method="get">
        <button class="button" style="vertical-align:middle"><span>Rent Without Reservation</span></button>
    </form>

    <form action="returnCar.php" method="get">
        <button class="button" style="vertical-align:middle"><span>Return a Car</span></button>
    </form>

    <form action="dailyrent.php" method="get">
        <button class="button" style="vertical-align:middle"><span>Generate Daily Rental Report</span></button>
    </form>

    <form action="dailyrentforbranch.php" method="get">
        <button class="button" style="vertical-align:middle"><span>Generate Daily Rental Report for Branch</span></button>
    </form>

    <form action="dailyreturn.php" method="get">
        <button class="button" style="vertical-align:middle"><span>Generate Daily Return Report</span></button>
    </form>

    <form action="dailyreturnforbranch.php" method="get">
        <button class="button" style="vertical-align:middle"><span>Generate Daily Return Report for Branch</span></button>
    </form>

    <form action="manageVehicle.php" method="get">
        <button class="button" style="vertical-align:middle"><span>Manage Vehicles</span></button>
    </form>

    <form action="manageVehicleTypes.php" method="get">
        <button class="button" style="vertical-align:middle"><span>Manage Vehicle Types</span></button>
    </form>

    <form action="ViewDataOfTheCompany.php" method="get">
        <button class="button" style="vertical-align:middle"><span>View Data of the Company</span></button>
    </form>

    <form action="startPage.php" method="get">
        <button class="button" style="horiz-align: right"><span>Back</span></button>
    </form>
</div>

</body>
</html>

