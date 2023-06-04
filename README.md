# Library-DataBase-Project
Project for the Databases 2023 class in NTUA ECE that simulates a Database about a Web Library of some schools. Users are able to find books, borrow, reserve and rate them.

*Team: Nikolaos Karakostas, Michail Dimitropoulos, Vassileios Delis.*

## Requirments-Specifications:

## Database Installation-Configuration project
### Prerequisite installation steps for macOS
**Download sql packages with homebrew:**
```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
brew install mysql 
```
- Open a terminal and login as the root user. This can be done by the following command: 
```bash 
sudo mysql -u root -p;
```
-Create a new MySQL user with thee following credentials as shown in the command
```bash 
CREATE USER 'root'@'localhost' IDENTIFIED BY 'root';
```

-Grant privileges to the user on the database using
```bash 
GRANT ALL PRIVILEGES ON library to 'root'@'localhost' WITH GRANT OPTION;
```

-To check if the privileges are set correctly you can see the grant table with this command:
```bash 
FLUSH PRIVILEGES;
```

- To clone this repository, use the following command:
```bash
git clone https://github.com/nikoskarako/Library-Database-Project
```
in a local working directory.

- Create the database in a DBMS that supports MySQL/MariaDB and run the scripts 
```library_schema.sql```
and
```library_insert_data.sql```

- Visit ```http://localhost:8889/login.php``` from your web browser.


## ScreenShots from our webpages:

<img width="1259" alt="Screenshot 2023-06-03 at 5 13 06 PM" src="https://github.com/nikoskarako/Library-DataBase-Project/assets/133955672/2f88bd12-f06c-4a37-9d2f-db508c5cc6f3">


<img width="1259" alt="Screenshot 2023-06-03 at 5 13 37 PM" src="https://github.com/nikoskarako/Library-DataBase-Project/assets/133955672/38535100-a705-4424-ad4d-90c9bad019b6">


<img width="1259" alt="Screenshot 2023-06-03 at 5 15 26 PM" src="https://github.com/nikoskarako/Library-DataBase-Project/assets/133955672/e8790275-3a70-48b6-b6f9-024ea3bc3bce">



# Disclaimer:
All data was randomly generated and any correlation to real world names, phone numbers, etc. is purely coincidental.



