# Car-Renting-Company-System

Using the website interface, you can login the car-renting system of this company as a customer or a clerk of the company (or sign up for new customer).
As a customer, you can do the following:
    (1) view the available cars based on the requirements you input
    (2) make/cancel a reservation for some vehicle
    (3) rent some vehicle with or without a previous reservation; return the vehicle before the due date.
    
As a clerk, you can do the following:
    (1) view the information of all branches and the status of all vehicles (whether rented, or reserved or available).
    (2) add/remove vehicles; modify the price or status for vehicles.
    (3) view the report of daily return from some specific branch which contains details of vehicles returned and the respective income.
    
Notes:
1. All the related data information (eg., information of cars or customers) are stored in "company_database.sql" as a database.

2. All the code for querying information and the web page design is in the folder "php files".
For example, the file "dailyreturnforbranch.php" contains the code to query the daily return for some specific branch along with the front-end code for this page.
