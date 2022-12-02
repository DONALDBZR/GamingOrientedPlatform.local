-- Creating the Database
CREATE DATABASE Parkinston;
-- Creating the Passwords table
CREATE TABLE Parkinston.Passwords (
    PasswordsId INT PRIMARY KEY AUTO_INCREMENT,
    PasswordsSalt VARCHAR(8),
    PasswordsHash VARCHAR(256)
);
-- Creating the Users table
CREATE TABLE Parkinston.Users (
    UsersUsername VARCHAR(32) PRIMARY KEY,
    UsersMailAddress VARCHAR(64),
    UsersPassword INT,
    UsersProfilePicture VARCHAR(128)
);
-- Creating the Accounts table
CREATE TABLE Parkinston.Accounts (
    AccountsId INT PRIMARY KEY AUTO_INCREMENT,
    AccountsLoL VARCHAR(64),
    AccountsUser VARCHAR(32)
);
-- Testing Codes
-- DROP DATABASE Parkinston;
-- SELECT * FROM Parkinston.Passwords;
-- SELECT * FROM Parkinston.Users;