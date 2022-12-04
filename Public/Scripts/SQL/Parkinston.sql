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
-- Creating the League Of Legends table
CREATE TABLE Parkinston.LeagueOfLegends (
    LeagueOfLegendsPlayerUniversallyUniqueIdentifier VARCHAR(128) PRIMARY KEY,
    LeagueOfLegendsGameName VARCHAR(64),
    LeagueOfLegendsTagLine VARCHAR(8)
);
-- Creating the Accounts table
CREATE TABLE Parkinston.Accounts (
    AccountsId INT PRIMARY KEY AUTO_INCREMENT,
    AccountsLoL VARCHAR(128),
    AccountsUser VARCHAR(32)
);
-- Testing Codes
-- DROP DATABASE Parkinston;
-- SELECT * FROM Parkinston.Passwords;
-- SELECT * FROM Parkinston.Users;
-- DROP TABLE Parkinston.Accounts;
-- DROP TABLE Parkinston.LeagueOfLegends;
-- SELECT * FROM Parkinston.LeagueOfLegends;
-- SELECT * FROM Parkinston.Accounts;
-- SELECT * FROM Parkinston.LeagueOfLegends WHERE LeagueOfLegendsPlayerUniversallyUniqueIdentifier = (SELECT AccountsLoL FROM Parkinston.Accounts WHERE AccountsUser = "test1");