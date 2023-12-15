CREATE DATABASE software_store;
USE software_store;

CREATE TABLE `User`
(
    user_id               int(10)      NOT NULL AUTO_INCREMENT,
    login                 varchar(255) NOT NULL UNIQUE,
    pass_hash             binary(128)  NOT NULL,
    username              varchar(255),
    account_creation_date date,
    account_type          varchar(6)   NOT NULL,
    UNIQUE INDEX index_user_id (user_id),
    PRIMARY KEY (user_id)
);
CREATE TABLE Review
(
    review_id         int(10) NOT NULL AUTO_INCREMENT,
    author_id         int(10) NOT NULL,
    software_id       int(10) NOT NULL,
    title             varchar(255),
    description       TEXT,
    date_added        date    NOT NULL,
    date_last_updated date    NOT NULL,
    INDEX index_date_added_review (date_added),
    PRIMARY KEY (review_id)
);
CREATE TABLE Rating
(
    rating_id   int(10) NOT NULL AUTO_INCREMENT,
    author_id   int(10) NOT NULL,
    software_id int(10) NOT NULL,
    mark        tinyint NOT NULL,
    date_added  date    NOT NULL,
    INDEX index_mark (mark),
    PRIMARY KEY (rating_id)
);
CREATE TABLE BugReport
(
    report_id                       int(10)     NOT NULL AUTO_INCREMENT,
    version_id                      int(10)     NOT NULL,
    user_id                         int(10)     NOT NULL,
    title                           varchar(255),
    description_of_steps_to_get_bug TEXT,
    bug_description                 TEXT,
    date_added                      date        NOT NULL,
    review_status                   varchar(20) NOT NULL,
    INDEX index_title_bug (title),
    PRIMARY KEY (report_id)
);
CREATE TABLE StatuteViolationReport
(
    report_id     int(10)     NOT NULL AUTO_INCREMENT,
    software_id   int(10)     NOT NULL,
    user_id       int(10)     NOT NULL,
    rule_point    int(10)     NOT NULL,
    description   TEXT,
    date_added    date        NOT NULL,
    review_status varchar(20) NOT NULL,
    PRIMARY KEY (report_id)
);
CREATE TABLE AccountChangeRequest
(
    request_id     int(10)     NOT NULL AUTO_INCREMENT,
    user_id        int(10)     NOT NULL,
    description    TEXT,
    justification  TEXT,
    date_submitted date        NOT NULL,
    review_status  varchar(20) NOT NULL,
    PRIMARY KEY (request_id)
);
CREATE TABLE SoftwareUnit
(
    software_id     int(10)      NOT NULL AUTO_INCREMENT,
    author_id       int(10)      NOT NULL,
    name            varchar(255) NOT NULL,
    description     TEXT,
    link_to_graphic varchar(255),
    is_blocked      tinyint(1)   NOT NULL,
    INDEX index_name_software (name),
    UNIQUE INDEX index_software_id (software_id),
    PRIMARY KEY (software_id)
);
CREATE TABLE SoftwareVersion
(
    version_id    int(10) NOT NULL AUTO_INCREMENT,
    software_id   int(10) NOT NULL,
    description   TEXT,
    date_added    date    NOT NULL,
    major_version int(10) NOT NULL,
    minor_version int(10) NOT NULL,
    patch_version int(10) NOT NULL,
    UNIQUE INDEX index_version_id (version_id),
    INDEX index_version_date_added (date_added),
    INDEX index_version_major_minor (major_version DESC, minor_version DESC),
    PRIMARY KEY (version_id)
);
CREATE TABLE SourceCode
(
    code_id    int(10)      NOT NULL AUTO_INCREMENT,
    version_id int(10)      NOT NULL,
    filepath   varchar(255) NOT NULL,
    PRIMARY KEY (code_id)
);
CREATE TABLE Executable
(
    executable_id       int(10)       NOT NULL AUTO_INCREMENT,
    version_id          int(10)       NOT NULL,
    target_architecture varchar(20)   NOT NULL,
    date_compiled       date          NOT NULL,
    filepath            varchar(1024) NOT NULL,
    UNIQUE INDEX index_executable_id (executable_id),
    PRIMARY KEY (executable_id)
);
CREATE TABLE Download
(
    download_id   int(10)   NOT NULL AUTO_INCREMENT,
    user_id       int(10)   NOT NULL,
    executable_id int(10)   NOT NULL,
    date_download timestamp NOT NULL,
    PRIMARY KEY (download_id)
);
CREATE TABLE Category
(
    category_id int(10)        NOT NULL UNIQUE,
    name        varchar(100)   NOT NULL,
    description TEXT NOT NULL
);
CREATE TABLE SoftwareCategory
(
    software_id int(10) NOT NULL,
    category_id int(10) NOT NULL
);

-- FOREIGN KEYS

ALTER TABLE SoftwareCategory
    ADD CONSTRAINT fk_SoftwareCategory_Category FOREIGN KEY (category_id) REFERENCES Category (category_id);
ALTER TABLE SoftwareCategory
    ADD CONSTRAINT fk_SoftwareCategory_SoftwareUnit FOREIGN KEY (software_id) REFERENCES SoftwareUnit (software_id);
ALTER TABLE SourceCode
    ADD CONSTRAINT fk_SourceCode_SoftwareVersion FOREIGN KEY (version_id) REFERENCES SoftwareVersion (version_id);
ALTER TABLE SoftwareVersion
    ADD CONSTRAINT fk_SoftwareVersion_SoftwareUnit FOREIGN KEY (software_id) REFERENCES SoftwareUnit (software_id);
ALTER TABLE SoftwareUnit
    ADD CONSTRAINT fk_SoftwareUnit_User FOREIGN KEY (author_id) REFERENCES User (user_id);
ALTER TABLE Executable
    ADD CONSTRAINT fk_Executable_SoftwareVersion FOREIGN KEY (version_id) REFERENCES SoftwareVersion (version_id);
ALTER TABLE Download
    ADD CONSTRAINT fk_Download_Executable FOREIGN KEY (executable_id) REFERENCES Executable (executable_id);
ALTER TABLE BugReport
    ADD CONSTRAINT fk_BugReport_SoftwareVersion FOREIGN KEY (version_id) REFERENCES SoftwareVersion (version_id);
ALTER TABLE BugReport
    ADD CONSTRAINT fk_BugReport_User FOREIGN KEY (user_id) REFERENCES User (user_id);
ALTER TABLE StatuteViolationReport
    ADD CONSTRAINT fk_StatuteViolationReport_User FOREIGN KEY (user_id) REFERENCES User (user_id);
ALTER TABLE StatuteViolationReport
    ADD CONSTRAINT fk_StatuteViolationReport_SoftwareUnit FOREIGN KEY (software_id) REFERENCES SoftwareUnit (software_id);
ALTER TABLE Rating
    ADD CONSTRAINT fk_Rating_SoftwareUnit FOREIGN KEY (software_id) REFERENCES SoftwareUnit (software_id);
ALTER TABLE Rating
    ADD CONSTRAINT fk_Rating_User FOREIGN KEY (author_id) REFERENCES User (user_id);
ALTER TABLE Review
    ADD CONSTRAINT fk_Review_User FOREIGN KEY (author_id) REFERENCES User (user_id);
ALTER TABLE Review
    ADD CONSTRAINT fk_Review_SoftwareUnit FOREIGN KEY (software_id) REFERENCES SoftwareUnit (software_id);
ALTER TABLE AccountChangeRequest
    ADD CONSTRAINT fk_AccountChangeRequest_User FOREIGN KEY (user_id) REFERENCES User (user_id);


-- Views

CREATE VIEW OpenAccountChangeRequest AS
SELECT AccountChangeRequest.request_id,
       User.username,
       AccountChangeRequest.description,
       AccountChangeRequest.date_submitted as `date`
FROM User
         INNER JOIN AccountChangeRequest ON User.user_id = AccountChangeRequest.user_id;

CREATE VIEW AccountView AS
SELECT login,
       username,
       account_creation_date,
       account_type
FROM User;

CREATE VIEW DisplayReview AS
SELECT User.username as 'author_name',
       Review.title,
       SU.software_id,
       Review.description,
       Review.date_added,
       Review.date_last_updated
FROM User
         INNER JOIN Review ON User.user_id = Review.author_id
         INNER JOIN SoftwareUnit SU on Review.software_id = SU.software_id;

CREATE VIEW OpenBugReport AS
SELECT BugReport.report_id,
       BugReport.title,
       SoftwareUnit.name as 'concerned_software_unit_title',
       BugReport.description_of_steps_to_get_bug,
       BugReport.bug_description,
       BugReport.user_id as 'author_id',
       SoftwareVersion.major_version,
       SoftwareVersion.minor_version,
       SoftwareVersion.patch_version
FROM BugReport
         INNER JOIN SoftwareVersion ON SoftwareVersion.version_id = BugReport.version_id
         INNER JOIN SoftwareUnit ON SoftwareUnit.software_id = SoftwareVersion.version_id;

CREATE VIEW ShortSoftwareSummary AS
SELECT SoftwareUnit.name            as 'software_name',
       User.username                as 'author',
       SoftwareUnit.link_to_graphic as 'graphic'
FROM SoftwareUnit
         INNER JOIN User ON User.user_id = SoftwareUnit.author_id;

CREATE VIEW SoftwareSummary AS
SELECT SoftwareUnit.name            as 'software_name',
       User.username                as 'author',
       SoftwareUnit.link_to_graphic as 'graphic',
       SoftwareUnit.description,
       SoftwareVersion.version_id,
       SoftwareVersion.date_added
FROM SoftwareUnit
         INNER JOIN User ON User.user_id = SoftwareUnit.author_id
         INNER JOIN SoftwareVersion ON SoftwareVersion.software_id = SoftwareUnit.software_id;


-- TRIGGERS

DELIMITER $$

CREATE TRIGGER SoftwareVersionCreated
    BEFORE INSERT
    ON SoftwareVersion
    FOR EACH ROW
BEGIN
    DECLARE last_patch_version INT;

    SELECT patch_version
    INTO last_patch_version
    FROM SoftwareVersion
    WHERE software_id = NEW.software_id
      AND major_version = NEW.major_version
      AND minor_version = NEW.minor_version;

    IF last_patch_version IS NULL THEN
        SET NEW.patch_version = 0;
        ELSE
            SET NEW.patch_version = last_patch_version + 1;

    end if;

END $$


CREATE TRIGGER CategoryDeleted
    BEFORE DELETE
    ON Category
    FOR EACH ROW
BEGIN
    DELETE
    FROM SoftwareCategory
    WHERE category_id = OLD.category_id;
END $$


-- PROCEDURES

CREATE PROCEDURE PurgeSoftware(software_id INT)
BEGIN
    DELETE FROM SoftwareUnit WHERE SoftwareUnit.software_id = software_id;
    DELETE FROM SoftwareVersion WHERE SoftwareVersion.software_id = software_id;
    DELETE
    FROM SourceCode
    WHERE SourceCode.version_id IN (SELECT version_id
                                    FROM SoftwareVersion
                                    WHERE software_id = SoftwareVersion.software_id);
    DELETE FROM Rating WHERE Rating.software_id = software_id;
    DELETE FROM Review WHERE Review.software_id = software_id;
    DELETE
    FROM BugReport
    WHERE BugReport.version_id IN (SELECT version_id
                                   FROM SoftwareVersion
                                   WHERE software_id = SoftwareVersion.software_id);
END $$

CREATE PROCEDURE BlockSoftware(software_id_in INT)
BEGIN
    UPDATE SoftwareUnit
    SET is_blocked = 1
    WHERE SoftwareUnit.software_id = software_id_in;
END $$

CREATE PROCEDURE UnblockSoftware(software_id_in INT)
BEGIN
    UPDATE SoftwareUnit
    SET is_blocked = 0
    WHERE SoftwareUnit.software_id = software_id_in;
END $$

CREATE PROCEDURE PurgeUser(user_id INT)
BEGIN
    DECLARE acc_type varchar(6);

    SELECT User.account_type
    INTO acc_type
    FROM User
    WHERE User.user_id = user_id;

    IF acc_type != 'admin' THEN
        DELETE FROM User WHERE User.user_id = user_id;
        DELETE FROM Rating WHERE Rating.author_id = user_id;
        DELETE FROM Review WHERE Review.author_id = user_id;
        DELETE FROM AccountChangeRequest WHERE AccountChangeRequest.user_id = user_id;
        DELETE FROM BugReport WHERE BugReport.user_id = user_id;
        DELETE FROM Download WHERE Download.user_id = user_id;
        IF acc_type = 'author' THEN
            DELETE
            FROM SourceCode
            WHERE SourceCode.version_id IN (SELECT version_id
                                            FROM SoftwareVersion
                                            WHERE SoftwareVersion.software_id IN (SELECT software_id
                                                                                  FROM SoftwareUnit
                                                                                  WHERE author_id = user_id));
            DELETE
            FROM SoftwareVersion
            WHERE SoftwareVersion.software_id IN (SELECT software_id
                                                  FROM SoftwareUnit
                                                  WHERE author_id = user_id);

            DELETE FROM SoftwareUnit WHERE SoftwareUnit.author_id = user_id;

            DELETE
            FROM Executable
            WHERE Executable.version_id IN (SELECT version_id
                                            FROM SoftwareVersion
                                            WHERE SoftwareVersion.software_id IN (SELECT software_id
                                                                                  FROM SoftwareUnit
                                                                                  WHERE author_id = user_id));

        END IF;
    END IF;

END $$

CREATE PROCEDURE ProcessAccountChangeRequest(request_id_in INT, isAccepted BOOL, justification_in TEXT)
BEGIN
    IF isAccepted THEN
        UPDATE AccountChangeRequest
        SET review_status = 'Accepted',
            justification = justification_in
        WHERE AccountChangeRequest.request_id = request_id_in;
        UPDATE User
        SET account_type = 'author'
        WHERE user_id IN (SELECT user_id
                          FROM AccountChangeRequest
                          WHERE AccountChangeRequest.request_id = request_id_in);
    ELSE
        UPDATE AccountChangeRequest
        SET review_status = 'Declined',
            justification = justification_in
        WHERE AccountChangeRequest.request_id = request_id_in;
    end if;
END $$

CREATE PROCEDURE DeleteSoftwareVersion(version_id INT)
BEGIN
    DELETE FROM SourceCode WHERE SourceCode.version_id = version_id;
    DELETE
    FROM Download
    WHERE Download.executable_id IN (SELECT executable_id
                                     FROM Executable
                                     WHERE Executable.version_id = version_id);
    DELETE FROM Executable WHERE Executable.version_id = version_id;
    DELETE FROM SoftwareVersion WHERE SoftwareVersion.version_id = version_id;
END $$


-- FUNCTIONS

# CREATE FUNCTION GetCommaSeparatedCategories(software_id INT)
#     RETURNS VARCHAR
# BEGIN
#     DECLARE listCategory text;
#
#     SELECT GROUP_CONCAT(name ORDER BY name SEPARATOR ',')
#     INTO listCategory
#     FROM Category
#     WHERE Category.category_id IN (SELECT category_id
#                                    FROM SoftwareCategory
#                                    WHERE SoftwareCategory.software_id = software_id);
#
#     IF listCategory IS NULL THEN
#         SET listCategory = 'uncategorized';
#     end if;
#
#     RETURN listCategory;
# END $$

CREATE FUNCTION GetMostPopularSoftwareAuthor()
    RETURNS INT
BEGIN
    DECLARE author_most_popular INT;

    SELECT author_id
    INTO author_most_popular
    FROM (SELECT author_id,
                 COUNT((SELECT(download_id)
                        FROM Download
                                 INNER JOIN Executable E on Download.executable_id = E.executable_id
                        WHERE E.version_id = SV.version_id)) AS 'downloads_per_software'
          FROM SoftwareUnit SU
                   INNER JOIN SoftwareVersion SV ON SV.software_id = SU.software_id
          GROUP BY author_id
          ORDER BY downloads_per_software DESC
          LIMIT 1) as author_downloads;

    RETURN author_most_popular;
END $$

CREATE FUNCTION GetMostPopularSoftwareUnit(category_id INT)
    RETURNS INT
BEGIN
    DECLARE software_most_popular_category INT;

    SELECT software_id
    INTO software_most_popular_category
    FROM (SELECT SU.software_id,
                 COUNT((SELECT(download_id)
                        FROM Download
                                 INNER JOIN Executable E on Download.executable_id = E.executable_id
                        WHERE E.version_id = SV.version_id)) AS 'downloads_per_software'
          FROM SoftwareUnit SU
                   INNER JOIN SoftwareVersion SV ON SV.software_id = SU.software_id
                   INNER JOIN SoftwareCategory SC on SU.software_id = SC.software_id
          WHERE SC.category_id = category_id
          GROUP BY software_id
          ORDER BY downloads_per_software DESC
          LIMIT 1) as software_category_downloads;

    RETURN software_most_popular_category;
END $$

CREATE FUNCTION GetBestQualitySoftwareUnit()
    RETURNS INT
BEGIN
    DECLARE software_most_quality INT;

    SELECT software_id
    INTO software_most_quality
    FROM (SELECT SU.software_id,
                 COUNT((SELECT(download_id)
                        FROM Download
                                 INNER JOIN Executable E on Download.executable_id = E.executable_id
                        WHERE E.version_id = SV.version_id))                 AS 'downloads_per_software',
                 COUNT((SELECT(report_id)
                        FROM BugReport BR
                        WHERE BR.version_id = SV.version_id
                          AND BR.date_added < CURDATE() - INTERVAL 3 MONTH)) AS 'bugs_report_per_software'
          FROM SoftwareUnit SU
                   INNER JOIN SoftwareVersion SV ON SV.software_id = SU.software_id
          GROUP BY SU.software_id
          HAVING downloads_per_software >= 100
          ORDER BY bugs_report_per_software
          LIMIT 1) as software_downloads_bugs;

    RETURN software_most_quality;
END $$

DELIMITER ;

-- Creating database users

CREATE OR REPLACE USER 'Administrator'@'localhost' IDENTIFIED BY 'your_password';
CREATE OR REPLACE USER 'SoftwareAuthor'@'localhost' IDENTIFIED BY 'MyPassword123';
CREATE OR REPLACE USER 'Client'@'localhost' IDENTIFIED BY 'MyPassword123';
CREATE OR REPLACE USER 'UnregisteredUser'@'localhost' IDENTIFIED BY 'MyPassword123';

-- Privileges system

-- Administrator

GRANT ALL PRIVILEGES
ON software_store.*
TO Administrator@localhost;

-- Software Author

GRANT SELECT, DELETE, UPDATE, INSERT ON software_store.Executable TO SoftwareAuthor@localhost;
GRANT SELECT, DELETE, UPDATE, INSERT ON software_store.Rating TO SoftwareAuthor@localhost;
GRANT SELECT, DELETE, UPDATE, INSERT ON software_store.BugReport TO SoftwareAuthor@localhost;
GRANT SELECT, DELETE, UPDATE, INSERT ON software_store.Review TO SoftwareAuthor@localhost;
GRANT SELECT, DELETE, UPDATE, INSERT ON software_store.SourceCode TO SoftwareAuthor@localhost;
GRANT SELECT, DELETE, UPDATE, INSERT ON software_store.SoftwareUnit TO SoftwareAuthor@localhost;

GRANT SELECT, INSERT
ON software_store.Download
TO SoftwareAuthor@localhost;

GRANT INSERT ON software_store.StatuteViolationReport TO SoftwareAuthor@localhost;
GRANT INSERT ON software_store.SoftwareCategory TO SoftwareAuthor@localhost;

GRANT SELECT, DELETE, INSERT
ON software_store.SoftwareVersion
TO SoftwareAuthor@localhost;

-- Client

GRANT SELECT ON software_store.SoftwareCategory TO Client@localhost;
GRANT SELECT ON software_store.Executable TO Client@localhost;

GRANT INSERT ON software_store.Download TO Client@localhost;
GRANT INSERT ON software_store.BugReport TO Client@localhost;
GRANT INSERT ON software_store.StatuteViolationReport TO Client@localhost;
GRANT INSERT ON software_store.AccountChangeRequest TO Client@localhost;

GRANT SELECT, INSERT, UPDATE, DELETE ON software_store.Rating TO Client@localhost;
GRANT SELECT, INSERT, UPDATE, DELETE ON software_store.Review TO Client@localhost;
GRANT SELECT, INSERT, UPDATE, DELETE ON software_store.SoftwareUnit TO Client@localhost;
GRANT SELECT, INSERT, UPDATE, DELETE ON software_store.SoftwareVersion TO Client@localhost;

-- Unregistered User

GRANT INSERT
ON software_store.User
TO UnregisteredUser@localhost;


FLUSH PRIVILEGES; -- Save privileges