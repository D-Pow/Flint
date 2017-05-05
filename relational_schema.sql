/**
 * Contains relational schema needed to make the functioning database
 * Includes a small amount of starting data
 */
CREATE TABLE User (
  username VARCHAR(15) NOT NULL,
  uname VARCHAR(25) NOT NULL,
  email VARCHAR(20),
  password TEXT NOT NULL,
  str_addr VARCHAR(25),
  ucity VARCHAR(25),
  ustate VARCHAR(25),
  interests TEXT,
  ccn INT NOT NULL,
  last_login DATETIME,
  PRIMARY KEY (username)
);

CREATE TABLE Follows (
  username VARCHAR(15) NOT NULL,
  follows VARCHAR(15) NOT NULL,
  PRIMARY KEY (username, follows),
  FOREIGN KEY (username) REFERENCES User(username),
  FOREIGN KEY (follows) REFERENCES User(username)
);

CREATE TABLE Project (
  pid INTEGER NOT NULL,           --INTEGER primary keys autoincrement in SQLite3
  username VARCHAR(15) NOT NULL,
  pname VARCHAR(15) NOT NULL,
  description TEXT,
  post_time DATETIME NOT NULL,
  proj_completed BIT NOT NULL,    --if project has been completed yet
  completion_time DATETIME,       --date the project was completed
  minfunds REAL NOT NULL,
  maxfunds REAL NOT NULL,
  camp_end_time DATETIME NOT NULL,--date the campaign ends
  camp_finished BIT NOT NULL,     --if campaign is completed yet
  camp_success BIT NOT NULL,      --if the min funds were reached in time
  PRIMARY KEY (pid),
  FOREIGN KEY (username) REFERENCES User(username)
);

CREATE TABLE Media (
  mid INTEGER NOT NULL,
  filename TEXT NOT NULL,  --size based on hash function
  PRIMARY KEY (mid)
);

CREATE TABLE Tags (
  tid INTEGER NOT NULL,
  name VARCHAR(12) NOT NULL,
  PRIMARY KEY (tid)
);

CREATE TABLE Comment (
  cid INTEGER NOT NULL,
  username VARCHAR(15) NOT NULL,
  pid INTEGER NOT NULL,
  comment TEXT NOT NULL,
  ctime DATETIME NOT NULL,
  PRIMARY KEY (cid),
  FOREIGN KEY (username) REFERENCES User(username),
  FOREIGN KEY (pid) REFERENCES Project(pid)
);

CREATE TABLE Likes (
  username VARCHAR(15) NOT NULL,
  pid INTEGER NOT NULL,
  ltime DATETIME NOT NULL,
  PRIMARY KEY (username, pid),
  FOREIGN KEY (username) REFERENCES User(username),
  FOREIGN KEY (pid) REFERENCES Project(pid)
);

CREATE TABLE Rating (
  username VARCHAR(15) NOT NULL,
  pid INTEGER NOT NULL,
  rating INT NOT NULL,
  rtime DATETIME NOT NULL,
  PRIMARY KEY (username, pid),
  FOREIGN KEY (username) REFERENCES User(username),
  FOREIGN KEY (pid) REFERENCES Project(pid)
);

CREATE TABLE Donation (
  username VARCHAR(15) NOT NULL,
  pid INTEGER NOT NULL,
  amount INT NOT NULL,
  pledge_time DATETIME NOT NULL,
  charged BIT NOT NULL,
  charge_date DATETIME,
  PRIMARY KEY (username, pid),
  FOREIGN KEY (username) REFERENCES User(username),
  FOREIGN KEY (pid) REFERENCES Project(pid)
);

CREATE TABLE ProjectUpdate (
  uid INTEGER NOT NULL,
  username VARCHAR(15) NOT NULL,
  pid INTEGER NOT NULL,
  comment TEXT NOT NULL,
  ctime DATETIME NOT NULL,
  PRIMARY KEY (uid),
  FOREIGN KEY (username) REFERENCES User(username),
  FOREIGN KEY (pid) REFERENCES Project(pid)
);

CREATE TABLE Pmedia (
  pid INTEGER NOT NULL,
  mid INTEGER NOT NULL,
  PRIMARY KEY (pid, mid),
  FOREIGN KEY (pid) REFERENCES Project(pid),
  FOREIGN KEY (mid) REFERENCES Media(mid)
);

CREATE TABLE Ptags (
  pid INTEGER NOT NULL,
  tid INTEGER NOT NULL,
  PRIMARY KEY (pid, tid),
  FOREIGN KEY (pid) REFERENCES Project(pid),
  FOREIGN KEY (tid) REFERENCES Tags(tid)
);

CREATE TABLE Umedia (
  uid INTEGER NOT NULL,
  mid INTEGER NOT NULL,
  PRIMARY KEY (uid, mid),
  FOREIGN KEY (uid) REFERENCES ProjectUpdate(uid),
  FOREIGN KEY (mid) REFERENCES Media(mid)
);

CREATE TABLE Searches (
  username VARCHAR(15) NOT NULL,
  search TEXT NOT NULL,
  stime DATETIME NOT NULL,
  PRIMARY KEY (username, search, stime),
  FOREIGN KEY (username) REFERENCES User(username)
);

CREATE TABLE ProjectViews (
  username VARCHAR(15) NOT NULL,
  pid INTEGER NOT NULL,
  vtime DATETIME NOT NULL,
  PRIMARY KEY (username, pid, vtime),
  FOREIGN KEY (username) REFERENCES User(username),
  FOREIGN KEY (pid) REFERENCES Project(pid)
);

CREATE TABLE TagViews (
  username VARCHAR(15) NOT NULL,
  tid INTEGER NOT NULL,
  vtime DATETIME NOT NULL,
  PRIMARY KEY (username, tid, vtime),
  FOREIGN KEY (username) REFERENCES User(username),
  FOREIGN KEY (tid) REFERENCES Tags(tid)
);




/*
 * Some insert data
 */
INSERT INTO User VALUES ('Bob', 'Bobby Smith', 'bob@email.com', 'f0e4c2f76c58916ec258f246851bea091d14d4247a2fc3e18694461b1816e13b', '123 Street St.', 'Brooklyn', 'NY', 'Jazz, music, bars, falling', 1111222233334444, '2017-03-25 19:03:12');
INSERT INTO User VALUES ('Alice', 'Alice Jay', 'altjay@email.com', 'f0e4c2f76c58916ec258f246851bea091d14d4247a2fc3e18694461b1816e13b', '474 14th St.', 'Manhattan', 'NY', 'Pottery, fire, cats', 1234123412341234, '2016-12-09 13:23:00');
INSERT INTO User VALUES ('Michael', 'Michael Scott', 'greatscott@email.com', 'f0e4c2f76c58916ec258f246851bea091d14d4247a2fc3e18694461b1816e13b', '712 W 4th St.', 'New York', 'NY', 'Jazz, music, videos, film', 9876987698769876, '2015-04-05 05:20:20');
INSERT INTO User VALUES ('Jordan', 'Jordan Meaux', 'jmo@email.com', 'f0e4c2f76c58916ec258f246851bea091d14d4247a2fc3e18694461b1816e13b', '973 Jay St.', 'Brooklyn', 'NY', 'Painting, swimming, pottery', 9999888877776666, '2017-04-11 04:36:05');
INSERT INTO User VALUES ('BobInBrooklyn', 'Robert Mash', 'bib@email.com', 'f0e4c2f76c58916ec258f246851bea091d14d4247a2fc3e18694461b1816e13b', '1000 MetroTech', 'Brooklyn', 'NY', 'Couch potatoing, surfing the webs, trolling', 0000111100001111, '2017-04-12 00:24:00');

INSERT INTO Project VALUES (201, 'Bob', 'Learn jazz', 'I want to learn jazz music.', '2015-03-20 17:12:23', 1, '2015-04-10 17:12:23', 500, 750, '2015-04-05 23:59:59', 1, 1);
INSERT INTO Project VALUES (199, 'Bob', 'Go skydiving', 'I want to learn how to skydive!', '2015-02-00 03:19:12', 1, '2016-02-09 23:59:59', 1800, 2100, '2015-04-10 23:59:59', 1, 1);
INSERT INTO Project VALUES (200, 'Bob', 'Home-brew project', 'I want to learn how to home-brew.', '2016-01-09 14:39:47', 1, '2016-12-09 14:39:47', 400, 900, '2016-08-19 23:59:59', 1, 1);
INSERT INTO Project VALUES (198, 'Bob', 'Learn rock', 'I really want to learn rock music. Help me!', '2017-02-11 19:36:05', 0, NULL, 200, 500, '2017-05-12 23:59:59', 0, 0);
INSERT INTO Project VALUES (202, 'Alice', 'Pottery', 'I want to start a pottery business! Help me buy paint and a kiln.', '2016-12-09 13:23:00', 0, NULL, 2000, 4200, '2017-05-12 23:59:59', 0, 0);
INSERT INTO Project VALUES (203, 'Michael', 'Make a movie about jazz', 'I would like to make a documentary about jazz, but have no cameras. Help fund my need!', '2017-05-05 10:59:59', 0, NULL, 1200, 1800, '2017-07-20 23:59:59', 0, 0);
INSERT INTO Project VALUES (204, 'Jordan', 'Paint project', 'I would like to learn to paint. Help me buy supplies and lessons.', '2017-05-04 04:36:05', 0, NULL, 800, 2000, '2017-05-18 23:59:59', 0, 0);
INSERT INTO Project VALUES (205, 'BobInBrooklyn', 'Make pretty couch cover', 'I would like to make a pretty couch cover.', '2017-05-04 00:24:00', 0, NULL, 200, 400, '2018-04-31 23:59:59', 0, 0);

INSERT INTO Donation VALUES ('Michael', 201, 600, '2015-04-01 12:23:09', 1, '2015-04-04 23:59:59');
INSERT INTO Donation VALUES ('Alice', 199, 2100, '2015-04-08 12:23:09', 1, '2015-04-10 23:59:59');
INSERT INTO Donation VALUES ('Michael', 200, 400, '2016-08-16 12:23:09', 1, '2016-08-18 23:59:59');
INSERT INTO Donation VALUES ('Jordan', 200, 100, '2016-08-14 12:23:09', 1, '2016-08-17 23:59:59');
INSERT INTO Donation VALUES ('Bob', 202, 1700, '2016-12-15 13:23:00', 0, NULL);
INSERT INTO Donation VALUES ('Bob', 204, 200, '2017-04-16 04:36:05', 0, NULL);

INSERT INTO Follows VALUES ('BobInBrooklyn','Alice');
INSERT INTO Follows VALUES ('Alice','Michael');
INSERT INTO Follows VALUES ('BobInBrooklyn','Michael');
INSERT INTO Follows VALUES ('BobInBrooklyn','Jordan');
INSERT INTO Follows VALUES ('Alice', 'BobInBrooklyn');
INSERT INTO Follows VALUES ('Bob', 'BobInBrooklyn');
INSERT INTO Follows VALUES ('Bob', 'Michael');
INSERT INTO Follows VALUES ('Bob', 'Jordan');
INSERT INTO Follows VALUES ('Michael', 'Jordan');
INSERT INTO Follows VALUES ('Michael', 'Alice');
INSERT INTO Follows VALUES ('Michael', 'BobInBrooklyn');
INSERT INTO Follows VALUES ('Jordan', 'BobInBrooklyn');
INSERT INTO Follows VALUES ('Alice', 'Bob');

INSERT INTO Rating VALUES ('Michael', 201, 4, '2015-04-05 12:02:59');
INSERT INTO Rating VALUES ('Alice', 199, 5, '2015-04-11 20:17:19');
INSERT INTO Rating VALUES ('Michael', 200, 3, '2016-08-19 08:37:04');
INSERT INTO Rating VALUES ('Jordan', 200, 5, '2016-08-18 12:23:09');

INSERT INTO Comment VALUES (1, 'Alice', 199, 'I want to learn, too!', '2015-04-10 03:19:12');
INSERT INTO Comment VALUES (2, 'Michael', 201, 'You did a fine job, sir.', '2015-04-05 05:20:20');
INSERT INTO Comment VALUES (3, 'Jordan', 202, 'Good luck with the pottery. I have always wanted to learn, too.', '2016-12-11 08:23:00');
INSERT INTO Comment VALUES (4, 'Bob', 203, 'Good choice of music. Jazz is the best!', '2017-03-25 19:03:12');

INSERT INTO ProjectUpdate VALUES (100, 'Alice', 202, "I'm learning a lot so far!", '2016-12-29 09:15:00');

INSERT INTO Likes VALUES ('Alice', 199, '2015-04-10 03:14:12');
INSERT INTO Likes VALUES ('Michael', 201, '2015-04-05 05:15:20');
INSERT INTO Likes VALUES ('Michael', 200, '2016-08-16 12:10:09');
INSERT INTO Likes VALUES ('Jordan', 202, '2016-12-11 08:23:30');
INSERT INTO Likes VALUES ('Jordan', 201, '2016-12-11 08:28:00');
INSERT INTO Likes VALUES ('BobInBrooklyn', 202, '2016-12-15 13:20:00');
INSERT INTO Likes VALUES ('BobInBrooklyn', 203, '2017-03-24 18:20:43');
INSERT INTO Likes VALUES ('BobInBrooklyn', 204, '2017-04-16 04:30:05');
INSERT INTO Likes VALUES ('Bob', 203, '2017-03-23 18:20:43');

INSERT INTO Tags VALUES (1, 'Music');
INSERT INTO Tags VALUES (2, 'Painting');
INSERT INTO Tags VALUES (3, 'Outdoors');
INSERT INTO Tags VALUES (4, 'Adventure');
INSERT INTO Tags VALUES (5, 'Film');
INSERT INTO Tags VALUES (6, 'Arts and crafts');
INSERT INTO Tags VALUES (7, 'Food and drink');
INSERT INTO Tags VALUES (8, 'Innovation');
INSERT INTO Tags VALUES (9, 'Jazz');

INSERT INTO Ptags VALUES (198, 1);
INSERT INTO Ptags VALUES (199, 3);
INSERT INTO Ptags VALUES (199, 4);
INSERT INTO Ptags VALUES (200, 7);
INSERT INTO Ptags VALUES (200, 8);
INSERT INTO Ptags VALUES (201, 1);
INSERT INTO Ptags VALUES (201, 9);
INSERT INTO Ptags VALUES (202, 6);
INSERT INTO Ptags VALUES (203, 5);
INSERT INTO Ptags VALUES (203, 8);
INSERT INTO Ptags VALUES (204, 2);
INSERT INTO Ptags VALUES (204, 6);


--Triggers
--Update the project if time has passed or funds have been met
CREATE TRIGGER end_campaign_insert AFTER INSERT ON Donation
BEGIN
UPDATE Project SET
    camp_finished = (CASE
        --Max funds already met before new donation
        WHEN (SELECT SUM(amount) AS funds FROM Project JOIN Donation USING(pid)
                WHERE Project.pid = new.pid GROUP BY pid)
            >= (SELECT maxfunds FROM Project WHERE Project.pid = new.pid )
        THEN 1
        --Campaign end time already met
        WHEN (SELECT camp_end_time FROM Project JOIN Donation USING(pid)
            WHERE Project.pid = new.pid)
        <= (SELECT DATETIME('NOW'))
        THEN 1
        --Otherwise, cancel
        ELSE camp_finished --RAISE(ROLLBACK, 'Cancel transaction')
        END),
    
    camp_success = (CASE
        --Min funds have been met
        WHEN (SELECT SUM(amount) + new.amount AS funds FROM Project JOIN Donation USING(pid)
                WHERE new.pid = Project.pid GROUP BY pid)
            >= (SELECT minfunds FROM Project WHERE new.pid = Project.pid)
        THEN 1
        --Otherwise, cancel
        ELSE camp_success--RAISE(ROLLBACK, 'Cancel transaction')
        END)
    WHERE new.pid = Project.pid;
END;


--same as above, except UPDATE instead of INSERT
CREATE TRIGGER end_campaign_update AFTER UPDATE ON Donation
BEGIN
UPDATE Project SET
    camp_finished = (CASE
        --Max funds already met before new donation
        WHEN (SELECT SUM(amount) AS funds FROM Project JOIN Donation USING(pid)
                WHERE Project.pid = new.pid GROUP BY pid)
            >= (SELECT maxfunds FROM Project WHERE Project.pid = new.pid )
        THEN 1
        --Campaign end time already met
        WHEN (SELECT camp_end_time FROM Project JOIN Donation USING(pid)
            WHERE Project.pid = new.pid)
        <= (SELECT DATETIME('NOW'))
        THEN 1
        --Otherwise, cancel
        ELSE camp_finished --RAISE(ROLLBACK, 'Cancel transaction')
        END),
    
    camp_success = (CASE
        --Min funds have been met
        WHEN (SELECT SUM(amount) + new.amount AS funds FROM Project JOIN Donation USING(pid)
                WHERE new.pid = Project.pid GROUP BY pid)
            >= (SELECT minfunds FROM Project WHERE new.pid = Project.pid)
        THEN 1
        --Otherwise, cancel
        ELSE camp_success--RAISE(ROLLBACK, 'Cancel transaction')
        END)
    WHERE new.pid = Project.pid;
END;


--If the new donation exceeds the max funds, lower the donation
CREATE TRIGGER lower_donation_amount_insert AFTER INSERT ON Donation
BEGIN
UPDATE Donation SET amount = 
CASE
    WHEN (SELECT SUM(amount) AS funds FROM Project JOIN Donation USING(pid)
            WHERE new.pid = pid GROUP BY pid)
        > (SELECT maxfunds FROM Project WHERE new.pid = pid)
    THEN
        new.amount + (SELECT maxfunds - SUM(amount) AS funds FROM Project JOIN Donation USING(pid)
            WHERE new.pid = pid GROUP BY pid)
    ELSE
        --Otherwise, cancel
        amount --RAISE(ROLLBACK, 'Cancel transaction')
    END
    WHERE new.pid = pid AND new.username = username;
END;


--same as above, except UPDATE instead of INSERT
CREATE TRIGGER lower_donation_amount_update AFTER UPDATE ON Donation
BEGIN
UPDATE Donation SET amount = 
CASE
    WHEN (SELECT SUM(amount) AS funds FROM Project JOIN Donation USING(pid)
            WHERE new.pid = pid GROUP BY pid)
        > (SELECT maxfunds FROM Project WHERE new.pid = pid)
    THEN
        new.amount + (SELECT maxfunds - SUM(amount) AS funds FROM Project JOIN Donation USING(pid)
            WHERE new.pid = pid GROUP BY pid)
    ELSE
        --Otherwise, cancel
        amount --RAISE(ROLLBACK, 'Cancel transaction')
    END
    WHERE new.pid = pid AND new.username = username;
END;


--Charge donors upon success of a campaign
CREATE TRIGGER charge_donors AFTER UPDATE ON Project
BEGIN
UPDATE Donation SET 
    --If the success of the campaign is not recent
    charged = (CASE
        WHEN new.camp_finished = old.camp_finished
        THEN RAISE(ROLLBACK, 'Cancel transaction')
        END),
    --If the project's campaign has just recently been changed to success
    charged = (CASE
        WHEN new.camp_finished=1
            AND
            new.camp_success=1
            AND charge_date IS NULL
        THEN
            1
        ELSE
            charged
        END),

    charge_date = (CASE
        WHEN new.camp_finished=1
            AND
            new.camp_success=1
            AND charge_date IS NULL
        THEN
            DATETIME('NOW')
        ELSE
            charge_date
        END)
    WHERE new.pid = Donation.pid;
END;