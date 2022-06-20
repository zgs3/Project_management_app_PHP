# Project_management_app_PHP

* Application which allows the user to see the list of working employees and currently active projects. Projects and employees data is fetched from MySQL database. 

Also the user is able to add new employees, projects and edit or delete them and all those changes are updated in MySQL database accordingly.

* Project was done as a Sprint 7 task while I was studying at Baltic Institute of Technology. 


## Getting Started

* To be able to use the app, you must first have "Apache" server application like "XAMPP" or similar web server app. You can find more info about "XAMPP" [here](https://www.apachefriends.org/).

* Also you will need a tool to manage MySQL database files like "MySQL Workbench". You can find more info about it [here](https://www.mysql.com/products/workbench/).

If you are using XAMPP and MySQL Workbench:

1. Clone this repository to .../xammp/htdocs/ directory (or download the files manually).

2. Open MySQL Workbench, create a new connection and open it. 

Note that in this project default connection username "root" and no password is used. If you intend to use other connection credentials, you must change the username and password in "sql_connection.php" file accordingly.


2. 1. Click "Server".

2. 2. Click "Data Import".

2. 3. Choose "Import from Self-Contained File."

2. 4. Provide the "projects.sql" file directory (Located in the "db" folder in cloned repository).

2. 5. Click "new" and type "projects_db" schema name.

Note that schema name must be "projects_db", because this name is written in the code, so the app would not work if the name was different.

2. 6. Click "Start Import".


3. Start "Apache" server in "XAMMP".

4. Open your prefered browser and go to localhost/Project_management_php/

Note that the app name in the URL must be the same as the directory name where "index.php" file is located. If directory name is changed, change URL accordingly. 

5. Lastly, log in using given user name and password:

  User name: Manager

  Password: 1234


## Description

* The app is connecting to MySQL database and fetching all the data from 2 separate data tables. 

* The user is able to create, edit and delete data.

* This app is password protected, so the user must first log in to be able to use the app. 


## Techniques used

Code is written in PHP.

Styled using raw CSS.


## Authors

Project made by me - Žygimantas Kairaitis. 

Find me on [LinkedIn](https://www.linkedin.com/in/%C5%BEygimantas-kairaitis-018a86193/).