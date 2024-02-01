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
    version_id int(10)      NOT NULL UNIQUE,
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
    category_id int(10)        NOT NULL AUTO_INCREMENT,
    name        varchar(100)   NOT NULL,
    description TEXT NOT NULL,
    PRIMARY KEY(category_id)
);
CREATE TABLE SoftwareCategory
(
    software_id int(10) NOT NULL,
    category_id int(10) NOT NULL
);
CREATE TABLE Token (
    token char(128) PRIMARY KEY,
    user_id int(10) NOT NULL,
    expires_at DATETIME NOT NULL
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
ALTER TABLE Download
    ADD CONSTRAINT fk_Download_User FOREIGN KEY (user_id) REFERENCES User (user_id);
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

CREATE OR REPLACE TRIGGER SoftwareVersionCreated
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
      AND minor_version = NEW.minor_version
    ORDER BY patch_version DESC
    LIMIT 1;

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

CREATE OR REPLACE PROCEDURE PurgeSoftware(software_id INT)
BEGIN
DELETE
    FROM BugReport
    WHERE BugReport.version_id IN (SELECT version_id
                                   FROM SoftwareVersion
                                   WHERE software_id = SoftwareVersion.software_id);
    DELETE
    FROM SourceCode
    WHERE SourceCode.version_id IN (SELECT version_id
                                    FROM SoftwareVersion
                                    WHERE software_id = SoftwareVersion.software_id);
    DELETE
    FROM Download
    WHERE Download.executable_id IN (SELECT executable_id 
                                    FROM Executable
                                    WHERE Executable.version_id IN (SELECT version_id
                                                                    FROM SoftwareVersion
                                                                    WHERE SoftwareVersion.software_id = software_id));
    DELETE
    FROM Executable
    WHERE Executable.version_id IN (SELECT version_id
                                    FROM SoftwareVersion
                                    WHERE SoftwareVersion.software_id = software_id);
    DELETE FROM StatuteViolationReport WHERE StatuteViolationReport.software_id = software_id;
    DELETE FROM SoftwareCategory WHERE SoftwareCategory.software_id = software_id;
    DELETE FROM Rating WHERE Rating.software_id = software_id;
    DELETE FROM Review WHERE Review.software_id = software_id;
    DELETE FROM SoftwareVersion WHERE SoftwareVersion.software_id = software_id;
    DELETE FROM SoftwareUnit WHERE SoftwareUnit.software_id = software_id;
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

CREATE FUNCTION GetCommaSeparatedCategories(software_id INT)
    RETURNS VARCHAR(255)
BEGIN
    SET @listCategory := (
        SELECT GROUP_CONCAT(name ORDER BY name SEPARATOR ',')
        FROM Category
        WHERE Category.category_id IN (
            SELECT category_id
            FROM SoftwareCategory
            WHERE SoftwareCategory.software_id = software_id
        )
    );

    IF @listCategory IS NULL THEN
        SET @listCategory := 'uncategorized';
    END IF;

    RETURN @listCategory;
END $$

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
GRANT SELECT, UPDATE, INSERT ON software_store.BugReport TO SoftwareAuthor@localhost;
GRANT SELECT, DELETE, UPDATE, INSERT ON software_store.Review TO SoftwareAuthor@localhost;
GRANT SELECT, DELETE, UPDATE, INSERT ON software_store.SourceCode TO SoftwareAuthor@localhost;
GRANT SELECT, DELETE, UPDATE, INSERT ON software_store.SoftwareUnit TO SoftwareAuthor@localhost;
GRANT SELECT, DELETE, UPDATE, INSERT ON software_store.SoftwareCategory TO SoftwareAuthor@localhost;

GRANT SELECT, INSERT
ON software_store.Download
TO SoftwareAuthor@localhost;

GRANT INSERT ON software_store.StatuteViolationReport TO SoftwareAuthor@localhost;
GRANT INSERT ON software_store.SoftwareCategory TO SoftwareAuthor@localhost;

GRANT SELECT, DELETE, INSERT
ON software_store.SoftwareVersion
TO SoftwareAuthor@localhost;

GRANT EXECUTE ON PROCEDURE software_store.DeleteSoftwareVersion TO SoftwareAuthor@localhost;


-- Client

GRANT SELECT ON software_store.SoftwareCategory TO Client@localhost;
GRANT SELECT ON software_store.Executable TO Client@localhost;
GRANT SELECT ON software_store.SoftwareVersion TO Client@localhost;
GRANT SELECT ON software_store.SoftwareUnit TO Client@localhost;

GRANT INSERT ON software_store.Download TO Client@localhost;
GRANT INSERT ON software_store.BugReport TO Client@localhost;
GRANT INSERT ON software_store.StatuteViolationReport TO Client@localhost;
GRANT INSERT ON software_store.AccountChangeRequest TO Client@localhost;

GRANT SELECT, INSERT, UPDATE, DELETE ON software_store.Rating TO Client@localhost;
GRANT SELECT, INSERT, UPDATE, DELETE ON software_store.Review TO Client@localhost;

-- Unregistered User

GRANT INSERT
ON software_store.User
TO UnregisteredUser@localhost;


FLUSH PRIVILEGES; -- Save privileges


-- Example data

INSERT INTO User VALUES (1, "admin", "$argon2id$v=19$m=65536,t=4,p=1$MkF0UEMzVm5ySERJNmQ0dA$2nkv2r+JLVW7rf0rCxY69afC9CmS36DXYgk0CFhsxtU", "Administrator", "2021-01-01", "admin");
INSERT INTO User VALUES (2, "emily7388", "enqtneykmezqltaraectumahapfigttcnjkwchdtzxacwjrnmsppjsdsgeqjpbhnmlggoybyabiqdwddqstcvricpzlcwgmuolqnrfcjwbmploibxbawvnsyjlchbnvw", "Emily", "2023-05-11", "author");
INSERT INTO User VALUES (3, "emily4071", "nrkinrtuvbbkddseelnnbliauiuvexqtipdlamkinacwksgzoblsnsoijuqphzuthmiwyownlpqpwmulkcnodcduumjxcdsxpsxzszudrrhxypxpaiffdzlbomrqfgbv", "Emily", "2023-10-17", "client");
INSERT INTO User VALUES (4, "john8308", "xyfxittfqpjardzdcwkgjuxvyywtvjhmnkahbozmujkvouwzjhdoognfhxijinepzamfmpdumpasjerswijcuohdhqomonypuvgurxudbjwbtfrgdtwfaeelnnipbhrw", "John", "2023-04-10", "author");
INSERT INTO User VALUES (5, "emily7079", "lrinxzdiznnggfsvutttvmqwjnghxjgjgxvffwpugbsbcfujeysbstyrwvobtfuobtbfkaunxbogpojyccdgqziezuzpqbgbrvcmzvasczxtvbpcihdovkuupjbmiwnu", "Emily", "2024-06-21", "client");
INSERT INTO User VALUES (NULL, "john5535", "wwlywqjiueqbqakrulyloxvshpdyaguidqgekdqtupcmjbyeuyaaajtytajysnyjfqksxgjrpaysyfjhimyruhvdgqzagvubojvkzipkhppiuykapojqudlyguunevbn", "John", "2023-11-16", "author");
INSERT INTO User VALUES (NULL, "robert2897", "zpsbveemctwonoxbvlugzhcisbqwhfspprhyazmajhgtzvuntarucaqhcajiwrcpxrxzhcfbniacclllqncqhupshutvjqtcavvorbmvxltanckxtyoqnveapakiuwpc", "Robert", "2024-09-09", "client");
INSERT INTO User VALUES (NULL, "robert5019", "yphnvpbgegfzwnwpgbjkgodjnluoplqwwulqywupcknuysxmqihjqfaagsytlykxqlxorokmzwbgyioeispjgomxbkwsczatijfbshhbwuukpjvxfnxwktvknkfhewyh", "Robert", "2023-10-10", "client");
INSERT INTO User VALUES (NULL, "emma4129", "cddieyjzkcvpnxmvjmaxuofxfyalwnuogjtaescykxajcdfrqjvpvompurmmbjlutusxghoghrqzxqgliixdxbomjpyvwoxjuketyfgqusxaugyokqaayybpjcjeuomo", "Emma", "2024-07-24", "client");
INSERT INTO User VALUES (NULL, "john2853", "huiddoxlvibxjaxkomekmrrnruudrdmkkeedaipppaglwszagflvqmtnfwjsmkjedcfvimqizjvfetzwlsborqrqssrejgysacepknhewszalkaykzxivcvwvuipumhu", "John", "2024-06-24", "client");
INSERT INTO User VALUES (NULL, "john5309", "akvdzrwsmypmzgxmoniyanccqaicwyjgmiuhvnbzzhjrwwtviwctfqlmizinztvdjqiulimacjyttbsvkzfgwnofvfqfefpzjximhudrhtjbmpgnotftazwaukkiofgz", "John", "2024-09-27", "client");
INSERT INTO User VALUES (NULL, "emma3608", "vsrztkghvqclporrkqzrdbkztnqhvtpgbzllzpcrwpzpdhxfzulnkrijnxzgbrhsisonbfcuoutdrgqbajyynqyuzfdrzlppwvdsjdtvfqgekrwmebealufeaahiiwfe", "Emma", "2023-03-21", "client");
INSERT INTO User VALUES (NULL, "emma6504", "sjodojmbqkpyzgyeuqknsqamvwkzpxfkpffhdlnmqggjabpgwkjfzaojiekktwsjfgffztstptryapzeljymgydgxvdgkyurkpliafwjdehnhbezwhgygppkidnazdga", "Emma", "2023-07-19", "client");
INSERT INTO User VALUES (NULL, "emily1240", "bpfmwekxkgmfwxwrdplhkcgqqxoiaxafzrngavlcdrxemehvfkrgbtdlwlpvzhzrkwtgyimgrxzlotaxsccpeswakmkngmxkaksjljeeuxbiiyjcohpsiymyhinjvflg", "Emily", "2023-02-28", "admin");
INSERT INTO User VALUES (NULL, "robert2974", "wifruzaihatevqevofxozpzadadwixvtssrayfscpfhkkrgjtvvqvmwgrfslhqdhyzakwynhvpyoxbrhyafipkbzseezgninrxontjrzxrukgwsdpiruatyjfwizstck", "Robert", "2023-01-20", "author");
INSERT INTO User VALUES (NULL, "emily7714", "aepssotfoeanvptmxjzhshwuahlsuzltubezjidfvdmctrbprexdvvipclmupnumvzwolxzksmapzznvtlioorcwniaqroypvehyigckphipeqhijgbosxwzqjrgxmix", "Emily", "2024-11-09", "client");
INSERT INTO User VALUES (NULL, "emma5670", "qnvvcnajofojakcnmtgbfcgynqofbcjesjollmcrnkmeaxlhxlleoruqkahsdqrkzjhlwbqsxrugciqzaxfjrotgryhflwjssvusbrmsuxheapsuvkshbsbjzvqagzjx", "Emma", "2023-09-26", "author");
INSERT INTO User VALUES (NULL, "emily6426", "efmcwopkykcrliomxuwlpgkskluiazlmygohcfiktzhutfdjayidpabccrurxqpkulhkyufhaoiatgyxpojrzxbnxksbpfbbzcunwtoaaoroxoxnqjquqjqshqnrhixq", "Emily", "2024-01-09", "author");
INSERT INTO User VALUES (NULL, "emily3202", "bpcbatvewocrgxibpycnmbnvwymnoxctxndvsvmzigeudwdnoxyswqteipsanjmvujenuyqksbqwrwybkwxluyvumlttbdozkmciyjjxbkhmnzuriphawbrpybdsnvsx", "Emily", "2023-03-22", "client");
INSERT INTO User VALUES (NULL, "emily7978", "pqtylngtkgfsjqsxzhiftlfewlpcewbxqtgarclkcoltsddnuvzjxibgqeekialhaaifwwndqfpksrcsphtoerzforuxbjespruulbtrgczajtokguuidwvumblpokvi", "Emily", "2024-04-25", "author");
INSERT INTO User VALUES (NULL, "robert5083", "nnpmtxcxeozcwwwcodwpmezjxuzublfppaxhasswxandiradhlcboplgczjstnzrkffowesbgcjfpdlxjfxjuwtbknztdyrygzjaeqezmrfwmarjxtfrfnfelrqntein", "Robert", "2024-01-05", "admin");
INSERT INTO User VALUES (NULL, "john7079", "bznieydbibucdhfrtftqnwhbvtcwtmihqrcmepmgqlyazaaxwdrotbbtiqvfitvzxtezijeftienzklosxthvkcdekgsqxlrttvtwtaejwfhdiggrzijutapujugqbau", "John", "2023-10-28", "client");
INSERT INTO User VALUES (NULL, "john5496", "xfqrsdftyqeslavfbbmwahfqnzlfxaacpjginsfwjxgsrdrackjizkoezrynbhryymclxdazjjrywqxaxyzjqbiqzlbnnqoiwujgquuyufxbknwzoeuzfqqdtrsirpdr", "John", "2024-01-20", "author");
INSERT INTO User VALUES (NULL, "robert1620", "xtkbnggcojeicqwhgycaukarmroarufulmpznbmlojinkrmdcvmctivcvtfgeihuaxexiihyspnksxaohjsryerwamxeyclgqajxermucvkhwrhoigkfvacuuecmcnux", "Robert", "2023-02-01", "author");
INSERT INTO User VALUES (NULL, "emily2520", "lyczkxebcdqgaoeacqgoagxylxbrgmvukjlpbryknynqbgkkvlxqbylwkecmdvaencurszpaeggiwvfpvcagsxbmtuyprbpkilopxegvtskpttflxiqaijcruvjitzkc", "Emily", "2024-01-07", "admin");
INSERT INTO User VALUES (NULL, "emma1209", "rzqglwhqhflvbchqxriehevkrreifkylawokckziclhqkwsoxxgqsangyrfxjltkhxwuclpztsgqccfapmvwwvkckudtpvmklhlgupdporcyzmyodlrcvlgwhtdnphcq", "Emma", "2024-02-09", "author");

INSERT INTO SoftwareUnit VALUES (NULL, 4, "QsMONoEfeH", "SMKT6eRslTlKiOo9fqBSI5VJ2p7bAN0R2aJ8msOxKsKoGy332e60ocq4f4wNuTWM4ZEfK0Z4ENZqQzxXjf3C2lHWtKGu3WR6o5jtsoFD1ZHZCxL4W1POBOkJqcm62vE7uzq0nxiLBNFLQaBZSHuaiZwolPK05sjoMLtQW0shEBD8921vXpCtA6uDqFEbV0d7GZk3bYMXxM52CZIWYi82zMp35MRFeLT11XlRPQpftljnsPrnS3QlJkfI0qAgzxM", "https://example.com/graphic", 1);
INSERT INTO SoftwareUnit VALUES (NULL, 8, "kSwKIPmTfS", "rRqjrRPdyMpN1dKkWLqmzMDRCfhIAZxO3OCZQe3NTKfgMvoL3WQjvpTpaFR8paHtvrmcL9VVQUzalfHCZFhEZK9WxSnOBepeaoIUe7lzl8h2j1EuzCds47bXI18myqztMaBpQIlM9WmWRUNOrVaG2ATxPeJpEtu1LfAQH9vHhiJdvkciacyzZ58xBJU9EG79TiFAZn581tB3mLGnW3B2U9NBiAM4DwQFnInES3Jx5oJJJc4uATXOfM1NXwtUf27", "https://example.com/graphic", 1);
INSERT INTO SoftwareUnit VALUES (NULL, 6, "JoCrWhymLJ", "ryKfp4d7e8yI38TtrUvHGQsuvBHtPpXGQmRcLWeJsWXm03aMRY1EAZlQ20E3U90MOenFAzbnndXRF89gp6SXlK9RS4AoUrfBufJi1CXBSIcUp7fyp70qk5ajvZWbgkLAOiR51s4YNMryHR6r7lZAsHpysMnc94C1csZMkn8g5cjdhRM5flKGszS6zJi9aXSIllymWWDrnpwoowqZMU33jbZnRAC9B5Ep5e8YPu9knt9wa2ERDucNaOovCzFI4r6", "https://example.com/graphic", 1);
INSERT INTO SoftwareUnit VALUES (NULL, 1, "zEEvRDKyym", "RnGrWiz8nM5g0TfM3rZR6QI4VaMF004GObEp1I3lmoAWkQqrbYwZO3XpVjKxLs8IVoEtoWZD9QIuhcHS4UIUg54XNBfc0mGfihdDCJCdLWbwOpO5Ew9EeTkf31skIgOzKda8flQeJnN0L3TnZu8Gt98SrYgeTW9JsippqpzomOZJW4sUXrbJI1zU98lIzyB1qiiEqk61eCRlL8ElfoFTEgxpuY6LimMchzxLUBARHNwcZLuWP3A7JN2ns77NYq7", "https://example.com/graphic", 0);
INSERT INTO SoftwareUnit VALUES (NULL, 7, "xmueeyKWkb", "wOo83CNQdzLKcNX9XQ0NKrwequT37o4EEwIgWSXaXGJmP5R6cq5KqDyKjjizkJX44Wnz3h3UY4tWKsQG5sD4n8qrk88jkLVC6kgVpZXU58J1STFsDfWtSH9wr80iXfirGY2aPB4qwUPhA1OCBCPpfkcywtIaXLdCpDtstwsjdewXGfLs0QbHO3u0QkPU5kZP8dbhyjYAt7mIkrhiT1y2eEEa833X7HZd5jbnSODRRIFTwWWbQGqb1SF9i3lnJ8o", "https://example.com/graphic", 1);
INSERT INTO SoftwareUnit VALUES (NULL, 4, "ceyvMMUGEn", "mP56Wm4ViDpvGS5a1GaYO3tw3vt0SDWyNx3jZvd8qdv0L27rpo4TTddMfR3Sn2yHBrwKplIRpet3bIzG717iIdzs5e94XNsdaMAGXB7S4JiNxhJIiHlho6MAXxhu3rRC1ckF61igqmTwExcATjhR5CAQwfmH9JVWGjTnL1ilFTX1dUh5iPZWmtBuCsVj2Oi4L6r4lwadRZnHRih0jdKy3rkCrXgjEAmOTxkS8RlQ4I10ahuLSJJPXDk9gnPcGvN", "https://example.com/graphic", 1);
INSERT INTO SoftwareUnit VALUES (NULL, 8, "JNdSYmblHf", "iWIXbS6JmqdQ6LRhCf3Jwgj90Kus8rUfpLNF1sVSmabWBulvOvqudlSfpmfd02oqJAe2LZwaRusB7FE01NgiMF7RBjXu6auwk4XFpLKkREtAwGX4EDktlfYvu0kJqm9hJMhFoUkaFSg5GQiDY8yrhp8r8SIhLQCQTnWZv39L4gWrNcqwGraRjWNWr2U5FL3D7xHVmapvBeBy1rDsDS4hwrK52HA7trZGYPNyWSmRX0UujTHMZov2Q9evbvJF5zK", "https://example.com/graphic", 1);
INSERT INTO SoftwareUnit VALUES (NULL, 6, "ZKDyzNgEAO", "MNTTdy0jOo321Bc9casCXCg4DxA7fvYLIneER7wDh4HaWHQjNPMJvuUpC35TbhefHfu18oYlFf3rhuTgeFYDiyVc2UD43SvNPzikx64G7E3Zt5dMLQkdZseRnDRCz9q0PIXO2UNQFexsqfkEBhLrIPgTustUv9uZJjgIe1ReqD0y2fL4I0YDfHlCDelOH7j590TycNmH7aB1maWT5IiRGrGMMGwdCknA4Oi5h2DEoPeL9v10s2GWp1wzX3H3iRu", "https://example.com/graphic", 1);
INSERT INTO SoftwareUnit VALUES (NULL, 4, "BmrRHjyMba", "ZWwYi5AxqUwC4eDTY4mWXA7ibZI2lDp9yetDanS5OPQvbx4mfsQoFWFYjyH2UThl12CNDGhNHBuc7yhfMUJ4NeVUOtQZuNGRAgyE4U7DaapRQqcj3thCYhWfTYwAc6IcEjDlPHahztP1tlkDPmmsmpGeEtlqUrTrJLLtLa05Eu9aChgXdTpTS9XX929LeE0wGJQ7a6Ct70T58IwTAuoLImzUygZMWIrsJKsZQOGgFmKmMzvjuUnLlC6wrbe63Ix", "https://example.com/graphic", 0);
INSERT INTO SoftwareUnit VALUES (NULL, 9, "EOkhjGiKmw", "RfKyl2APyl80tKsSRpzqURPsFZguodZ1etRc0cKONPeW7E6rOAkbhRckJubTLojiIk5LJmAjnHH3fkLNVy5Znh4fH93YQpLW2nJWh0bKSEdoaOn9GRdRGyii4iZMVYEA83OyVjgAtnbKOOpBptOIC0n5VAlI9Y5TTjGDDejHtL61m0PsDHTCgu9Vt1Eae9oqBUyNPAC3vPSKz0kEaVmzTwVzzKXCu7iUD6vAVYGNgpTObDlmoYOHB2biaQYydfj", "https://example.com/graphic", 0);

INSERT INTO SoftwareVersion VALUES (NULL, 10, "VDlScvrT9UC4Q90xeH6Len8LRwkFnpp53SWZMKrlo49ODUjSJbeKaRDsTTrgrIaFyG3lrm0JJrOknDeGblrhKj90LjhSVkoaKwA2B5UrRod7a3WXH8sH8p1M3X2n0O1MWOC11H1pjvwzU8BsP49bDYCcmu8HpQXWqTIY1UBq6V3bGdawSHnDdXzUSCkzQq015wOoo26IThCZLW9KwbBdkKVKaOpep59zVtTR1pz6XnlIG89BX1wW8lxN0UIGwDK", "2024-08-26", 3, 8, NULL);
INSERT INTO SoftwareVersion VALUES (NULL, 2, "pnKSGX02xgtbhe34VOHTprYY4ROFgh5udojYyovf5bDmblhDykDUCZeRffZp9PXZnxHJf9ZMSo4sQjsokTq9NRsm10ozElvzZ4MLsSNZZymCKmPM7ilvtZ0VtJVL3RIuQXOLukg2if0M0u0cwoOkDb12adIsNurSiEVDY95yQBUgp378PdNNPMFf1G8PrVQ1LwZolGMv3vTdyNTl5latPd278eY0jbB3muAiegfyE9ZJO3HBwwzX1jtOtyMTOH0", "2023-05-19", 10, 2, NULL);
INSERT INTO SoftwareVersion VALUES (NULL, 6, "tiUWM3iEdsDJ1M7VPcXIOGbdW5xxNvfFYdPhyRinXzAun9Uf0BJP87VK97ULoJNj31xhjyMhgTs8UGy0mUOo06N0NEobyPJGZWpehKZhKSLAs8RnyNiCgEkYceWJ1EzXausagHChLJHcD8vC34o0P99tSZx5japQiz2cXsmnOXgRedPm6DFIa17dDdAmKqwt3ofvXXQXgQ8Yt7jTJGWmSXBtL5rRxeodRqM1syoBmP7KoTCGVPbuNsJj4Lp5SSc", "2023-03-23", 4, 4, NULL);
INSERT INTO SoftwareVersion VALUES (NULL, 9, "fd36EXrBEBS2168hUIXe5Hi9bQXMec4sFKyxXu2rf8BYdrlHSay0Ef9AEiiCb5islbbdBySRY1XqJvGW1AzSD5OapPIGxq6OtQVFP10aER8J5JMQfqZibQj5NqtEySghmhoYgKNbsvH5tvQPzMMXEsAAo6VX24FpHW8P2Jrw9XeWWFwlyoVaY8oOKpf3Y9jUxzhEG7cKQkUn5svmISTGMO374aozXWLvEJZLb1UnI2UYJaN4UXAY5r0uNzXsUST", "2023-04-18", 5, 10, NULL);
INSERT INTO SoftwareVersion VALUES (NULL, 1, "lyVfAu6MQaTRiY4h4C3OICGFdkCBf3pNYZvTvhdUbTSvZN2nptqrEr46gYx7BCMyYg7HTObjnGinIqca2y7IxaX8WxLtHgmtTozFbM8NkpB3PgZGapG6gqL4OndaTNclEXBSZSRMrFzkra95Kd4OFxaQ9TFZJnhGSOrdy3MWLNawRLrDO5KN8FpNVYYqzMTN1LMdWhYiA3S339ugnlKLVCAo1T9DL97sh819KAlS4d6nNV2et6y8cuHFYSolv1b", "2024-09-19", 10, 3, NULL);
INSERT INTO SoftwareVersion VALUES (NULL, 3, "LFhA93MBPmH9N6QZods2bnjkZ4JCMLRXtaNVAxMDUDRqShYSPZ6jsNFmYlUJg7A4kUlkHitvxUGwJ7iqZ9CvZM6Ka8I2oE5IjhfOMUM6VT1ywUKZNWaKonCBqVrrGsCfou6MjTeNRW4ULmkb7X0V5tbqKVx103vbC9LWObTPVsc36craDOvtgrrnFV86qqFdxdj8hTDqGkNfBdww8MBWELEguYdvAL1FwjhXxhKz1p5TuqGlTxrCiebFusYeOHW", "2023-05-16", 5, 8, NULL);
INSERT INTO SoftwareVersion VALUES (NULL, 2, "5Mv5CQV8Y0EZCQlsHvhZzmal2kqwqx68d3hrPaCnkfXC9Ql2acxVSDQaneajQhdUJUFGXSTMA7qv8ulFPTd5zSSJ9PnxADZBA9HRhuw7p73TkfT8B6eNn81JCPjMPQQtTHJWoKgzocUJbYEGV5mrLukEDYSkBRzXvIKfllmpGEsvmg3HGV7aDaefNLLBWFmMSRbSsXwG8pv6R4CFX3PGIHRE9neD5P5xpVeR957iMHIZVLsx3UXHO1a1igcOHMh", "2024-06-27", 6, 1, NULL);
INSERT INTO SoftwareVersion VALUES (NULL, 4, "QuJ3DmwtVtcZypHM8xulEr6XZnhQn79UXLiDTLdZ5WnG96N6CzEWTAbtZSykpAXVNFYkJ6aO9WSmDebNa2ObMgdd8V0mbaAXNpAvmDdQaL4mEoe6mq4JEEcAjLIMZHiMem3OszQeLwuhnii6JUZN6utQtAfpACEqo67S7bxjGaOI2S8oYfWYSRtsXfUZGpD10JxMZcRIuk3FkDZnMfksA7l0AnBy3Z4vLMgW2N9Kjz2sBLVKa651grWUmqt3aHa", "2024-04-03", 8, 7, NULL);
INSERT INTO SoftwareVersion VALUES (NULL, 6, "vTJSXUvIeycw2gKNZyKMZGfA9wrrLubHsfBreWjTODAnyjsLBuGH69FTkrErqoghaMiwZruQL9YUwYCTpDKbWbkVPFE7F79ImR01lqDCf2azKXzjWEsH57QoIsMBlkHGSc0LUyzIJ9U429WFd6NldOHrqTaq5EMNm2D16W015BO7jRa0Y5I3rVdRwa85MTAYydrtOcIMlLQI3He9seQd9At9VbUbD4CMRE5oeAadoyTOSZBxBwEiPIkauRPLddL", "2024-07-19", 8, 1, NULL);
INSERT INTO SoftwareVersion VALUES (NULL, 1, "ZbhA5pQOv2l9QmsBS4ka3eZs2ln0nidQWPkfBxwOiojpPaFdQgyv2yTTMBU2G69cX8kyxVQ5MlU22BH0Ahthz0XkrOb6C6QDq32EWfc6eX3QUthM3zImhMvg6PRpbycMSvlrXZKN1vmRHfiR05S1KZoZPo7QyyvFWUeZhT9IIHiKymBvCxLeZyuwFPFuksdpydNy9pbZuRohqWnVQDjPojvNipP6XZKt4yXhEuTS064H4yaZJ5kmCwr1h6ZsjEc", "2023-08-27", 10, 9, NULL);


INSERT INTO SourceCode VALUES (NULL, 7, "/path/to/source/code/7/");
INSERT INTO SourceCode VALUES (NULL, 5, "/path/to/source/code/5/");
INSERT INTO SourceCode VALUES (NULL, 10, "/path/to/source/code/10/");
INSERT INTO SourceCode VALUES (NULL, 4, "/path/to/source/code/4/");
INSERT INTO SourceCode VALUES (NULL, 9, "/path/to/source/code/9/");

INSERT INTO Executable VALUES (NULL, 1, "arm", "2023-09-05", "/path/to/executable/4/app.dmg");
INSERT INTO Executable VALUES (NULL, 1, "x64", "2024-05-02", "/path/to/executable/9/app.deb");
INSERT INTO Executable VALUES (NULL, 1, "x64", "2024-11-26", "/path/to/executable/7/app.dmg");
INSERT INTO Executable VALUES (NULL, 1, "arm", "2024-01-20", "/path/to/executable/3/app.msi");
INSERT INTO Executable VALUES (NULL, 1, "arm", "2024-04-09", "/path/to/executable/1/app.msi");
INSERT INTO Executable VALUES (NULL, 2, "x64", "2023-11-28", "/path/to/executable/8/app.msi");
INSERT INTO Executable VALUES (NULL, 2, "x64", "2024-05-14", "/path/to/executable/4/app.app");
INSERT INTO Executable VALUES (NULL, 2, "x86", "2024-02-27", "/path/to/executable/5/app.msi");
INSERT INTO Executable VALUES (NULL, 2, "x64", "2023-10-24", "/path/to/executable/1/app.deb");
INSERT INTO Executable VALUES (NULL, 2, "x86", "2023-11-23", "/path/to/executable/8/app.exe");

INSERT INTO Category VALUES(NULL, "Games", "Games are fun");
INSERT INTO Category VALUES(NULL, "Utilities", "Utilities are useful");
INSERT INTO Category VALUES(NULL, "Productivity", "Productivity is important");
INSERT INTO Category VALUES(NULL, "Security", "Security is important");
INSERT INTO Category VALUES(NULL, "Education", "Education is important");
INSERT INTO Category VALUES(NULL, "Graphics", "Graphics are important");
INSERT INTO Category VALUES(NULL, "Networking", "Networking is important");
INSERT INTO Category VALUES(NULL, "Development", "Development is important");

INSERT INTO StatuteViolationReport VALUES (NULL, 3, 2, 15, "There is a statute violation in the software store. There is hate speech in the app. Please remove it.", '2024-01-24', 'Pending');
INSERT INTO StatuteViolationReport VALUES (NULL, 3, 1, 15, "There is a statute violation in the software store. There is hate speech in the app. Please remove it.", '2024-01-14', 'Pending');

INSERT INTO AccountChangeRequest VALUES (1, 1, 'I would like to a new software author because I can', 'no justification yet', '2017-01-01',  'Pending');
INSERT INTO AccountChangeRequest VALUES (2, 2, 'I would like to a new software author because I can', 'no justification yet', '2018-11-01',  'Pending');
INSERT INTO AccountChangeRequest VALUES (3, 3, 'I would like to a new software author because I can', 'no justification yet', '2017-11-15',  'Pending');
INSERT INTO AccountChangeRequest VALUES (4, 4, 'I would like to a new software author because I can', 'no justification yet', '2017-11-15',  'Pending');

