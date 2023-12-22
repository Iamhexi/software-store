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

CREATE PROCEDURE PurgeSoftware(software_id INT)
BEGIN
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
    DELETE FROM SoftwareVersion WHERE SoftwareVersion.software_id = software_id;
    DELETE FROM SoftwareCategory WHERE SoftwareCategory.software_id = software_id;
    DELETE FROM StatuteViolationReport WHERE StatuteViolationReport.software_id = software_id;
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

CREATE OR REPLACE PROCEDURE PurgeUser(user_id INT)
BEGIN
    DECLARE acc_type varchar(6);

    SELECT User.account_type
    INTO acc_type
    FROM User
    WHERE User.user_id = user_id;

    IF acc_type != 'admin' THEN
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
            FROM Executable
            WHERE Executable.version_id IN (SELECT version_id
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

        END IF;

        DELETE FROM User WHERE User.user_id = user_id;
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
    DELETE FROM BugReport WHERE BugReport.version_id = version_id;
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

CREATE OR REPLACE FUNCTION GetMostPopularSoftwareAuthor()
    RETURNS INT
BEGIN
    DECLARE author_most_popular INT;

    SELECT
	author_id INTO author_most_popular
    FROM
        (
        SELECT
            author_id,
            COUNT(DISTINCT D.download_id) AS downloads_per_software
        FROM
            SoftwareUnit SU
        INNER JOIN SoftwareVersion SV ON
            SV.software_id = SU.software_id
        INNER JOIN Executable E ON
            E.version_id = SV.version_id
        INNER JOIN Download D ON
            D.executable_id = E.executable_id
        GROUP BY
            author_id
        ORDER BY
            downloads_per_software DESC
        LIMIT 1
    ) AS author_downloads;

    RETURN author_most_popular;
END $$

CREATE OR REPLACE FUNCTION GetMostPopularSoftwareUnitInCategory(category_id INT)
    RETURNS INT
BEGIN
    DECLARE software_most_popular_category INT;

    SELECT software_id
    INTO software_most_popular_category
    FROM (SELECT SU.software_id,
                 COUNT(DISTINCT D.download_id) AS downloads_per_software
          FROM SoftwareUnit SU
                   INNER JOIN SoftwareVersion SV ON SV.software_id = SU.software_id
                   INNER JOIN SoftwareCategory SC ON SU.software_id = SC.software_id
                   INNER JOIN Executable E ON E.version_id = SV.version_id
                   INNER JOIN Download D ON D.executable_id = E.executable_id
          WHERE SC.category_id = category_id
          GROUP BY software_id
          ORDER BY downloads_per_software DESC
          LIMIT 1) as software_category_downloads;

    RETURN software_most_popular_category;
END $$

CREATE OR REPLACE FUNCTION GetBestQualitySoftwareUnit()
    RETURNS INT
BEGIN
    DECLARE software_most_quality INT;

    SELECT software_id
    INTO software_most_quality
    FROM (SELECT SU.software_id,
                 COUNT(DISTINCT D.download_id) AS downloads_per_software,
                 COUNT(DISTINCT BR.report_id) AS bugs_report_per_software
          FROM SoftwareUnit SU
                   INNER JOIN SoftwareVersion SV ON SV.software_id = SU.software_id
                   INNER JOIN Executable E ON E.version_id = SV.version_id
                   INNER JOIN Download D ON D.executable_id = E.executable_id
                   INNER JOIN BugReport BR ON BR.version_id = SV.version_id
          WHERE BR.date_added > CURDATE() - INTERVAL 3 MONTH
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

INSERT INTO User VALUES (NULL, "emily7388", "enqtneykmezqltaraectumahapfigttcnjkwchdtzxacwjrnmsppjsdsgeqjpbhnmlggoybyabiqdwddqstcvricpzlcwgmuolqnrfcjwbmploibxbawvnsyjlchbnvw", "Emily", "2023-05-11", "author");
INSERT INTO User VALUES (NULL, "emily4071", "nrkinrtuvbbkddseelnnbliauiuvexqtipdlamkinacwksgzoblsnsoijuqphzuthmiwyownlpqpwmulkcnodcduumjxcdsxpsxzszudrrhxypxpaiffdzlbomrqfgbv", "Emily", "2023-10-17", "client");
INSERT INTO User VALUES (NULL, "john8308", "xyfxittfqpjardzdcwkgjuxvyywtvjhmnkahbozmujkvouwzjhdoognfhxijinepzamfmpdumpasjerswijcuohdhqomonypuvgurxudbjwbtfrgdtwfaeelnnipbhrw", "John", "2023-04-10", "author");
INSERT INTO User VALUES (NULL, "emily7079", "lrinxzdiznnggfsvutttvmqwjnghxjgjgxvffwpugbsbcfujeysbstyrwvobtfuobtbfkaunxbogpojyccdgqziezuzpqbgbrvcmzvasczxtvbpcihdovkuupjbmiwnu", "Emily", "2024-06-21", "client");
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
INSERT INTO SourceCode VALUES (NULL, 10, "/path/to/source/code/10/");
INSERT INTO SourceCode VALUES (NULL, 4, "/path/to/source/code/4/");
INSERT INTO SourceCode VALUES (NULL, 9, "/path/to/source/code/9/");
INSERT INTO SourceCode VALUES (NULL, 7, "/path/to/source/code/7/");

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


INSERT INTO Download VALUES (NULL, 1, 8, "2024-12-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-03-06");
INSERT INTO Download VALUES (NULL, 10, 8, "2023-02-08");
INSERT INTO Download VALUES (NULL, 6, 6, "2023-04-26");
INSERT INTO Download VALUES (NULL, 1, 10, "2023-12-24");
INSERT INTO Download VALUES (NULL, 9, 6, "2024-08-18");
INSERT INTO Download VALUES (NULL, 1, 5, "2024-06-15");
INSERT INTO Download VALUES (NULL, 9, 4, "2024-07-25");
INSERT INTO Download VALUES (NULL, 1, 9, "2023-07-10");
INSERT INTO Download VALUES (NULL, 6, 4, "2024-09-20");
INSERT INTO Download VALUES (NULL, 10, 3, "2023-09-19");
INSERT INTO Download VALUES (NULL, 10, 5, "2024-09-08");
INSERT INTO Download VALUES (NULL, 4, 7, "2023-12-24");
INSERT INTO Download VALUES (NULL, 2, 4, "2023-06-18");
INSERT INTO Download VALUES (NULL, 10, 6, "2023-11-06");
INSERT INTO Download VALUES (NULL, 5, 4, "2024-02-24");
INSERT INTO Download VALUES (NULL, 5, 10, "2024-12-12");
INSERT INTO Download VALUES (NULL, 2, 5, "2023-07-19");
INSERT INTO Download VALUES (NULL, 1, 6, "2024-05-11");
INSERT INTO Download VALUES (NULL, 9, 5, "2023-10-16");
INSERT INTO Download VALUES (NULL, 2, 10, "2024-12-23");
INSERT INTO Download VALUES (NULL, 6, 5, "2023-12-05");
INSERT INTO Download VALUES (NULL, 3, 3, "2024-08-08");
INSERT INTO Download VALUES (NULL, 6, 1, "2023-09-25");
INSERT INTO Download VALUES (NULL, 6, 8, "2024-02-27");
INSERT INTO Download VALUES (NULL, 10, 8, "2023-03-22");
INSERT INTO Download VALUES (NULL, 9, 1, "2024-03-27");
INSERT INTO Download VALUES (NULL, 5, 5, "2024-11-05");
INSERT INTO Download VALUES (NULL, 8, 5, "2023-03-14");
INSERT INTO Download VALUES (NULL, 6, 5, "2023-01-14");
INSERT INTO Download VALUES (NULL, 2, 1, "2024-12-10");
INSERT INTO Download VALUES (NULL, 9, 8, "2024-04-12");
INSERT INTO Download VALUES (NULL, 7, 9, "2023-05-20");
INSERT INTO Download VALUES (NULL, 1, 2, "2023-07-16");
INSERT INTO Download VALUES (NULL, 5, 10, "2024-06-04");
INSERT INTO Download VALUES (NULL, 5, 2, "2024-09-20");
INSERT INTO Download VALUES (NULL, 5, 10, "2024-07-28");
INSERT INTO Download VALUES (NULL, 3, 5, "2024-04-04");
INSERT INTO Download VALUES (NULL, 8, 6, "2024-10-03");
INSERT INTO Download VALUES (NULL, 7, 7, "2023-05-13");
INSERT INTO Download VALUES (NULL, 3, 6, "2024-09-25");
INSERT INTO Download VALUES (NULL, 6, 7, "2023-09-10");
INSERT INTO Download VALUES (NULL, 2, 2, "2023-03-19");
INSERT INTO Download VALUES (NULL, 7, 4, "2023-01-02");
INSERT INTO Download VALUES (NULL, 2, 4, "2023-04-16");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 9, 4, "2024-02-11");
INSERT INTO Download VALUES (NULL, 6, 5, "2023-05-04");
INSERT INTO Download VALUES (NULL, 8, 9, "2024-04-07");
INSERT INTO Download VALUES (NULL, 3, 1, "2024-03-19");
INSERT INTO Download VALUES (NULL, 3, 1, "2024-03-19");

INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");
INSERT INTO Download VALUES (NULL, 2, 1, "2023-10-27");


INSERT INTO Category VALUES (NULL, "QCReWjQxwy", "HUWrCpFR7XiNTJajWniOo4MtKnEltMUrbnb6LMfggGqC9KchuW1foaTgt5ISGfCaAkgJjTPHmzlxCHCcZ4ESnZ5fWw1RCAtPdbgglOqkfv8Jvoau808yGopvgr0irh98Co0qXBsh9wdAcsUNTfcRILxP2QGuugIqo5wUyiO0TxmZ9jYLtuWyhrkL76DOwYxkxl5m1KzzVGPcN5hDjijcpDepEdcqMJYGPIqOkpDk4nk0X2beKsagitPGxiWGD3I");
INSERT INTO Category VALUES (NULL, "TZWqWgGOhh", "fepSo4lM5UndP60RK5kQBGtcvTaNXokeed8jVnO2iWuoJrZKKHn96Jg6oJGo9YhgwY8FcEtUNJHnc22hhKafictn05oQ6sFvPaZQBRBhr0W4Lr1TZK0bXL4ysWv3BGFUqZ47kohudpGLoMKusegmpHpqJKQCtZm9QZRGnmnR9DoA2kqsQcRk4VxOaHpkkDIh1WF5KTV0alCMFBgPzCHaWXE8Qy1S94hkoKU8V9lHblwszmuwfEU0K9zHpZKTfFB");
INSERT INTO Category VALUES (NULL, "YwMwObfasO", "n8IJQG1KC0Z0CsPJNmnstgBxzsLR3qUILJm5erE9vxni8ygHXeC4Qu5n4JXqNryxVJaptlknSlCyOPk5GurHn2y6vlmLxD8yRhFxB4PsLaoJusYa1k8PSY4E5yxfZYODBgdKiGZy1jdFMEbZbLjyrhp8759yJJRMrk51ElAA0LvmHMqp6ErdAxO7sTC8iccXt4q3KRvokIJx3550054ysQeFfN6ahWbG1SIXhzhCoPdmErz7t5BJTd4gOV7fDOM");
INSERT INTO Category VALUES (NULL, "eLYXvFXJlM", "Mf7JPA0teJYpDDBlM02l3tNEK217LSO8VVM9Icxnrk7SehINAcy5vTUZCInFy4t5A0cgI1g7w3dgjck2YmZAyoi0fkqJtbRk3jtXjvIrEGNhlixKkTRHbZuoOnzxtvPVZFOul3UCHsH80MYE391xjKNrxlLH7QV7yorNMJbSheRuYxhX33qIluWg3teGBfzGQgx2Em94FUNgbFhskPgr2eNwh00Gqat8ieKLDRyFgUmIaAc0bXU8zgyKfq67mRB");
INSERT INTO Category VALUES (NULL, "YtQwEVzSFi", "N4of8PPQOdwRo4qFKHbrpxWoPGO3f3QVqqZTpDBPsTmI92pRtBMVP6RIzlnVAAMB7sXFi7rY7rFib5SgkjsV08JcKOK7h3xQKcQfs9FhUAM11JqCvnd4eQCdRZVpdzVy1wcx23Q4fgSdYryQigJJFcOn0rGr7qysceXWhtIHgHsI6enphMF2ySHbt6IygrKC4K5iOu1iP52VqOkNAGwcr9b4v0Opcnzm1UlDVbnB2nVOHjEyzq31zms2f07KsET");
INSERT INTO Category VALUES (NULL, "bMxITAwDmi", "Ow8pg7Uu6112QwPARbSF3UHsqksaHaf0jxrGxTVq1LnLtYVR6GG5M8aUf0WHqk368wlkKw454fmBhJXKY5SXcDgDEtQ2sya6wSG1xMNySXOYfWqo2CfwAsyfnukSCcJK0GM9KGbpRB3OuIiVJqkIMbhcteDggjdYTE5LlduwwSeeW7y4eFKmLpsN6FA4WyrOvuhQitBmBnDbZuWC8oqZUb9lpZNT8wRSps4IfcMYduE6Ggjr09Tz0WFkPC2970X");
INSERT INTO Category VALUES (NULL, "xUGGcvXPvc", "apgFXZKeiEUsKpXYyBLVVj1UGo2GTzTMkjo7Kfoy0CTSlP5r8MQaqrRjCPxqQYwf5wPU2azvHIiyQsc5HymzwKJ8sVeJiLWP2BIHOeuukL41Gauw155jGHX3JSoGi37AK6NP7A0xSUQVEZT8DBzQA2R7KIooWpvwV1oF7qwc9cG9nQTpm3B8EzU4aL2KbrvH92APPx5Ypg25qS8k4Me8KX7M2UAZGsP139UPJKnkyeOjUTWElkVDpPRrO3QD0Oe");
INSERT INTO Category VALUES (NULL, "OoZXPkDkJc", "Mu04bDEBF8fi0umjWmR796wbmsaDVCiHjYa0Aq7QfQY1RVyHJQc7tQBGQkK33sMkfzprTYgFOaXYX4JU1tB79J5dlg4XKhS5jgkwO0Ar0E6TxCw6YrJlw85MRtGEkoW7ArEuhGMDu25eHldauJLR63Ry9RmvwZkbxyebe4wAdOcX79pWuBy4N4ifMzYLLldeeHeUiI19h7GaxcFqGBoaETXOsaKkzs5hxVhXHxR3x3Uttw47FhpYIQptmszAfPj");
INSERT INTO Category VALUES (NULL, "ByysDOFWqK", "qc9HYbmt973FeLezCADTrPh1Ph0nPWqvkodVUJZtOhsaCQjxINek62iqqwi8CJr1MejKOe1tsw8bLR0kC48w1ZZ4r0bQCVHFSulr1DJEV44wPgZ6qRRFKTjYQzhWkth2v2yBEJIPA9QkNZnx6Vrf1kdckqv0GSU5AkzXIlJUdkg0OR7WFmqfCIrwapDZGhrQJ1gXImNf2p1U526PA1ULXFzK0Jz5xTK67Cpj7Zr51uVZPyGXmlbmsi3QOJBuAWP");
INSERT INTO Category VALUES (NULL, "qIRzgpaUEn", "Mshqz05m9w9ILHmpkQLakvLFbDtIYKyeTXVOL15FXyt2IztoPIKkzEnXT982GZ1lqwbI06WdeyIhmoHGV0kYSgEWK93qiGNRu3ZI0bRNxLOjDmJbmBPkhD1CBYcBaA78KqXkUir3cz7rGQowQN0GmcGMdb6rvUHJ2cv5wUGX9i4ha2yu56kTk8LiRdt63MFDhgji5RTMh3xeBbqvVOVrZxqYoeLyd53SNDGvrd3ava89xvYn1cjV4oWd4MJUYOj");

INSERT INTO SoftwareCategory VALUES (1, 2);
INSERT INTO SoftwareCategory VALUES (8, 6);
INSERT INTO SoftwareCategory VALUES (1, 4);
INSERT INTO SoftwareCategory VALUES (5, 9);
INSERT INTO SoftwareCategory VALUES (10, 3);
INSERT INTO SoftwareCategory VALUES (5, 9);
INSERT INTO SoftwareCategory VALUES (6, 5);
INSERT INTO SoftwareCategory VALUES (3, 5);
INSERT INTO SoftwareCategory VALUES (10, 1);
INSERT INTO SoftwareCategory VALUES (8, 9);

INSERT INTO Rating VALUES (NULL, 4, 9, 4, "2023-12-23");
INSERT INTO Rating VALUES (NULL, 6, 6, 2, "2024-12-07");
INSERT INTO Rating VALUES (NULL, 9, 4, 5, "2023-01-17");
INSERT INTO Rating VALUES (NULL, 2, 4, 1, "2023-06-23");
INSERT INTO Rating VALUES (NULL, 7, 5, 5, "2024-08-21");
INSERT INTO Rating VALUES (NULL, 10, 10, 1, "2023-07-05");
INSERT INTO Rating VALUES (NULL, 6, 9, 4, "2024-11-03");
INSERT INTO Rating VALUES (NULL, 6, 7, 4, "2024-09-24");
INSERT INTO Rating VALUES (NULL, 6, 7, 5, "2024-12-02");
INSERT INTO Rating VALUES (NULL, 4, 4, 1, "2023-05-21");

INSERT INTO Review VALUES (NULL, 9, 5, "Needs improvement", "JiQHado7hZHBqZvcEVJ24bL1cGiER9QJObmgCcqeNBcwsINcOV7UBq3cg62CZMQOYlqGGhzvK7C4jcsl6T6FpHm1bf0FOnbSgOfW6IbwNMbsY4KtUunafvBjrDzK6EqKMJraUfsw97lt3k7QMx9YjQMdQ5kr889YWfFsoLpNYchdCoYMaZXU30NLfPYvCi9tQuKFgf2BIMXeMgqXYWmPygyvpkdicgmKMPrn1ou7KaT4VYxICnKEMTxT7p2EGuw", "2024-07-16", "2023-12-08");
INSERT INTO Review VALUES (NULL, 10, 9, "Buggy but promising", "t94DNmMNHMkiYNMQUUwXt8R8swUqi3at1oj4hmE2xKoB7TNLVFr1mDaNp2Q8L1upw6JOX57Cmbs1ElA2PGy0yUbZ0EXJ2lrxqvWS0BOazTnBOUZUpdPERBauWAcEsZGMW9IqQD2o6VqZmISnMe5ylIF0ndhWJ37LH9v0wLDPH7BntQ0zhCJZqZlp3PoScMY8HtRkH9qTJJ9ziZ7DpGr7RJuZ7alxmeywGfoASUaEwcFOqPSOaxJDjDbXrDaDT0x", "2024-05-11", "2024-05-05");
INSERT INTO Review VALUES (NULL, 10, 7, "Excellent work", "lBqOniQBnuGumrDS7IMC3ydE2doLH3BKkL6AK3OkjQioJd3uNpsllyKoJiHeOw7tyIJmlX0KwqGMUPVxGd921DtABrJVmnEU3hQfJPFJCV51UTMwBsjC0CktM64rkeUSoaaq8Fmi2eBIBUFevS7L4egcgyVHsA5FiH8WrWx0wLgSyAbzxfHi7LUVFIoOanFkPGCDjRSH5rFFU3j8krqOfNQFLXuWRLVh5Hg6LHUR3R0SYeE2oczZPL30JDkVoPY", "2023-01-14", "2023-06-21");
INSERT INTO Review VALUES (NULL, 10, 1, "Buggy but promising", "dq4E6xIsvYzBguNDYqAiX5Z0rWslP00HsKBZm4FWgj7wJOTGm7Lfxa3YgUZOSm03W80pCutsBSo99xJNSFaPVW3hwtOpoeFPy8aiqBcZGGA5u9rgit1qkhvS356Vk2QmjetHrgrSghP86BPPJsZvTxk0D6mut98ZtG9kzvJEoQFG7z4js8FAoyLCbqaL1hxXjhONQE0f04BTxy378VoKjDIi0FvTwMBaRPSeAW8Bcgeg3BVzAjKfx7iqonpAwBB", "2023-01-22", "2024-08-22");
INSERT INTO Review VALUES (NULL, 7, 6, "Excellent work", "O9eGsuJeAMEdcTTv9gSs3CRY4bvDlak1uEXFVv9eeczGdYYi7K2SyJiilNNTBk4VUpwchc4dkqDKXTvE5dSN3m1hvHTZOAovkbQn5tYMP3RC0ZtFpkbXOSTm9zEWo7cQezG8KSAyLmM063QvXQxvwDOqwCsJbQLMCi4iOMRNeSImp2uRBPkDMR7EwYcQqfHCEedQ8Lm0oXvv8a57GijXLdXVziXiAboWiaT5dGW516t2Rsji3hMH0pb0gNRKc5a", "2023-06-02", "2024-12-10");
INSERT INTO Review VALUES (NULL, 6, 7, "Buggy but promising", "Wl8wAKuwVh6UruKIBIhvZhv6S1tH9id0UN5r8oOkyVcF8Z8SQTbuqyLtw5Fr8TSwxcbCQu8tKGQ7V5KE0gEtoVLOxotbdjESQVy5gjOqTttywdPVPd175DPiSMYlqXC50uKawUpUE0uIwBbIwoo9ANth8kXhEyRoXhIReeXTiS6u22UbWdPpH5oAH7s0d0BCodHxtdnax87Zg0VHMl0gmhLxEDqGav40kd1aNopIRPOXXiSTSlnHiUzYbOlFInz", "2023-07-01", "2023-02-19");
INSERT INTO Review VALUES (NULL, 6, 5, "Buggy but promising", "vZXgHFhzeBaugKQD4p4nLvzQOpz8CRpNbwqY1ZlkOAgNqr2lwN5XZxyJdxq9zU3Q6sKOhis6eZmwCX0Mo97Au28WzAUyAYHPGkOOYlSFCXAM8b0gdh2EAwDrLQJ3f8ApIbYnQb9LDlhXCFwXxVs3vZvMXQ8pLGblAm3t43fNz7jCThzgOzyutIj6g0LcfM2KybdymAE7HcqsgisNAvyzjiNNvmPkXxBrbNn4HphWnXipMOM3b9R4E2FBwm8HUDF", "2023-06-14", "2023-11-12");
INSERT INTO Review VALUES (NULL, 8, 3, "Needs improvement", "e7KDcXR7fTfRFyFw3Pu2bwYX7tL2ImMwSSLVcYuzbqojoKyNxvDrioMzBQJ0XtOkj1aJb7GRvSjOyVPtwZNSltbi8Px4ZpkxjWpWH9viWFUBY24VYYV76kOeg5gGQGNoAFSQASss2psoJHlm9AcmSGB0A8HN6sqAlsBRxGXTnxjinCdvIm9HIdl7ktPCx2o6YJpYjTM7QwwaUaGufGXVbLbflzTLAfXStQpRs5l3dG7PKAEDRRudy6xLyzsvkND", "2024-12-05", "2023-04-21");
INSERT INTO Review VALUES (NULL, 4, 9, "Excellent work", "Tjvq7pCqJby0PQTcC89s8dCCzsGZ1Rl6jT8zIOyhCbJiHRSSb03NRWrXko5tHLgI49Ea5B3OGca3qZKa7KLyUHwnMLD1Jibv9yO8ULqPrT6uSbPiNHHDcuBkJLvLPZNbj2YY0HJ6RFojWTCe6YMuHCmja1zXac1QnFKEYCFzjDOwynbwBt3lEXqLgaCCmZJVbmujSuZhr0vIuPepJbHyFkEvfaDKaoPqHI6130fx0sEeEGH9tdskyA87S5m5K2j", "2023-02-05", "2024-04-14");
INSERT INTO Review VALUES (NULL, 3, 8, "Excellent work", "zTm2QHSkfV8xTOxTLu1teucZyr2YMSQUAK503XeASDwKpY5204SPIgvkcEiZAImcteFzZdgE5EM26qxAzDIZhTHAMFxSZZKEWwxgIPq0NZDRh5dSavYgO2K1KbRu9dsi6dFdDTj7kwCKZEblQ7OJ2ib8OQtoI9ug6xx1aG0drdIHDBiKCqzfrWxRNVSOo9e7TYfAYsQISwF1mqultFQuUIP0MQofBFRF2webmzM4b7HtvTpb7XrcQKJI0K7rSJ6", "2024-07-21", "2024-05-01");

INSERT INTO StatuteViolationReport VALUES (NULL, 5, 3, 8, "AFVhO7YBmzOYCcyYV4stWnBlNIWwZz5XyqZpdZzBiQNABnz6QXSKMfgXkaMHJwuYrnq50E8zSpvJrQ7voVKxhhtHMbKUKKE2Qej2ti2yfEDnSmRr3wUeFoPbCxdup3quL9VhOXujcWSJhsm90ciNovveUfBv7YWGV5h5SDuNanOMYsf4JnYXIfc3xdN6CqQj0EhlS8WS1poLbEQ4o5iFdSHiyCwJEcW8FwbjzTkX6aTq0Lgrgv3OxW1spWDRyIp", "2023-11-15", "pending");
INSERT INTO StatuteViolationReport VALUES (NULL, 8, 1, 2, "HCDuvtETOyr9cXUUKT4NETjBO2xFxFfsCQsZJM4ChoEtEoX0jWv4oPsV4bOoc24gvPTcRYDNZ9Ud0OPbbhHMzTzh5NGwlAjBQBOCeJKilhlqf4YLgRr7VDidXJADLQm6PpZtiOs9v1UjN4CfGRsq2aSvHq1oLiWgVCx7FJUjz5G9d8Shrt0hsN0Kc7mFr4JbobB32qnXrDzYPWCl1tTsdcXGsVr77JbYo5yBVsHwqHCsmqUqTN7IKRrlC9wvr50", "2024-11-19", "resolved");
INSERT INTO StatuteViolationReport VALUES (NULL, 2, 4, 8, "Pg6dqfECHM5V3QO45XpOZnk5O88tPHzfbBJmm6Q5sW0Oiv1J8I8A8dtkYo9vxNg361a9iRvosfLN3aqZQlrSl3z5gwz7xlfxyrgmBBWjxltkn7qFcG5rhhwaiId33FJCc5UachHg3c10wkJ9Se7qkfgpSKBD2hi0dFxizgmsE3pinCLXuE1yTTJsuXG61dW1ilQwfRzBBYQeG1GKbGE9yL9WO2iuSgJlpUS4jJVzIW3oijb5KANtgczl6c140E4", "2023-08-17", "pending");
INSERT INTO StatuteViolationReport VALUES (NULL, 3, 1, 5, "95LxYGZzeT0WjzqAh5YkWkvQ9fcFv5RQa2QVVnMYUX4KJlUcoI1HriInbiIG0v5Wx6tqEzTqi0JoP5pvc3ipTKnRhztW8DQKJMU4wmGPfcqCEcCN7QUSzQQlCvrtI5VXQpTbYMfHvObU9pWE3zQC2M2tNvW3M6BhXa6LvCdBsUjqQDbzJighwE6wmkx3yoQ21jRKJQWz2RGqJ7eL5zUSFsFfIGHbSABYorxlTyUbpGo1Ia2HgP2xqvq6XYHEzlA", "2024-07-24", "resolved");
INSERT INTO StatuteViolationReport VALUES (NULL, 4, 4, 1, "aMJ7RQfTF3RwzNR5AhZZnnt1HHf7hQzrZR6iRTmjrXseoL2Uc7bz9zeCrKoQW4XkYVxW1UFRjUF2gvQishIvPUI91ATBhK98ytEq6M4cB9OYI7Z8b4fNwDN4FpASmityBdpRjLOs4WMcgNsyZvvbXLBEVzWEZXcR8Cnt5JOsmmQNHzKjCq8AHuxLHPRdZ3Mh3BvqyMXoe0mKmdlLJ3tk2TwKBMoO1CFlmBZ7NMgxRiz9aOSUdciiCTgH2TZLK3F", "2023-05-15", "resolved");
INSERT INTO StatuteViolationReport VALUES (NULL, 4, 1, 2, "BiNwGkfbzdQpOomlB6XP6IsHNrvpfduNPUUWER1pEZCzIZzCcsvc27ISOIeLtWbazjG1o85gWhelhFLuEMR3dwir1DYzxVfFeQmyzEtast6KJmBmM8abxxe57ORFq5XDhrYgaa6JOZkGNa9gquY6MpuFkCpiInOmWFnrjFfNCkA816Y1mJbWR5uZ059g9XGJxlUJEecUNVggR0MYIfL89ums9OpnTrot8z593BIUh938yUr8nQXUJOnUSdlK3mN", "2023-02-02", "resolved");
INSERT INTO StatuteViolationReport VALUES (NULL, 3, 6, 2, "YF5J5LrsLg3ikQfPOuM3oghD9V34CRDh4ZzHMpdDe4dfo71ArYT5NQcu1usze7uE4hPVdChOde2ZGuk0K1UBQgAqMdnE19nlQ1GuOO8HDBSeg2NKkFxPnR9Izbjvy9bFHrPXou4ndhURDSY4YnFM712JSJeSAQQ4TonNCX5y72OnHUqKWs8Gl8QLFR9MSVSDOK9eZWDM4PWwjlb54LIYng2ELTqihz1Iuuh0EahCu3AKVKrdTx1T6VE4MjgTre9", "2024-06-21", "pending");
INSERT INTO StatuteViolationReport VALUES (NULL, 10, 5, 5, "08a9ejlDOerdI0bLNxK7PjEG3JEZMECP4pYXmFqsULdbrK2bcxq9kTo4SD8qdVr9Vup1rhQCBY8i9nXCq2TyaSxmZDu2iJLmbAIJE1wYVywoaijJBAgBu6K5avODhQNo4wR0a3uuH9yY4JxhBEkc6dZIbDPdX4aQeoPgC0We65I5WTPJgeWwuxMv7eRAn5yRCtCj1ZAUMSV2FRBV4aiCN5pQIOOPJrAf2mxrtHmZJ4Gb1hiSfF1DLqoaaKFgm5n", "2023-10-10", "resolved");
INSERT INTO StatuteViolationReport VALUES (NULL, 3, 4, 7, "8kMz6mUGG6iPoYn6YbMulzNw7UWNJWASEacuWAyfLkMHJSHdbZXISVc3ifByasYTFnZGJTfcVBrOKAvYuahq67bTLD05kszYLbv68lOeL99zLdY11tXkoCCBTgzPrmhfO6gtm5FgIvyWBgQA1DCRnt25qhDGEoQtcb1ZdW0mQNvkZRnQPVXRHMuCuwF9WPpqmUuwzE4ac0FMwXpc69cScPQgg5H5de78qZ1qK6Ac9yJpmika4daQAz4efOrZv0F", "2023-03-05", "pending");
INSERT INTO StatuteViolationReport VALUES (NULL, 4, 4, 5, "pcA79YUybjxLMVmUcxBxqX4EZ2pYYqcpWh3Mr56KWvhYJNlBznTnbrrfWzbhJqbJNZo4f8pXOWmvZrtsPQ68uqNAKkKpXbVahUx1b5u7lorCRGQLxpx8va9tzfoMntg3QiaCvf085CqF7GexeFHwzSCvbVTifGd5CkmUyxxTL9XxcCQZogqMFkiyypHdFWe7cIgOaMwX6uGeCxd2HLmQgbXhOmQTHsPik8gcn37vdn6Tqar5iC1og3nSD6PWx7v", "2023-01-12", "resolved");

INSERT INTO BugReport VALUES (NULL, 6, 3, "Bug in 6", "63qdVuhw3VLaOK1AnnmOFtyjwNMfAhMkcZqZw7AWEo09KoxzdyO5Hp9Odp7WOBy2PRRe6Bov7nqwa5yeFlPJR0wozeYSYczSfhUTqFsBAxBi0YOoXHMvrpd2tcWeoaMIGNOwOxhIG8C9eNLyDJ4wHD1CQvD8xRNKmNZwQoDWfsoLE1nodozU4IIUZU9W1Y1cUMCqz5kbsDlnFZpl77JIASSV3DWwsOjJSZnG6QpaUkuDNS11Gn45WvlfjBegsSr", "OGqPBPtiSu23hmCfdrZFbb2u5dPLQLYxW88YxpB5CWTmVUlMmTJ17YPORWbBfpxzhPMwN5pi9MUQ8cS1xzpqpNtrfPb81cBtYyHdZPRFBTu1H9ZqOLwOMZfK8bp7S47jffx1009F7Qfb61euTjFNva8AhxZoZ26xntMlJCnUQgpjQdFKjcbNeL001Xr96dt5Joju4FBEiyN11e9lvfMS6EE9PiLNuL3oGXYGDLTQHzmVpOJ0SNfvIegonlzt5IW", "2023-06-09", "resolved");
INSERT INTO BugReport VALUES (NULL, 5, 1, "Bug in 5", "MyXvmNKqNoeSmuKTFWvedet95rMlSI9HsIRVU3c5n4s7JrRkgfon6kZs2j0dMH4twlJ8jeJsWmVxmjbeoiAaszoVZXON9tY5wvzdJb0HtRhE4VBRIkCGvJTGbqTO0OrZ5d87mDalZIsfIl7mF7rRaqyaOPbhQ8tKe5uyqmo9aVc4Doe1ZwlCo10sRIA7J0db0BMqLboPpdVPRuB0wad8m6mPvdketmbAsNmrugzbHbRht1htU0hfsgasyX4CeZa", "4NfDzBrOmsk9IDJJWPDFimQK1y1zbUVvAVOrXZVD0li9d8CiKCYJ0Pfe8KENpnGxAl5kka83cfUgR5m5VpCSWribu9SGRDSDjLcoYYFQln5G7He6jI5dQWRMmfSQr352RxOwalszcUczjfxuEqqAL64pnFawbtc5RcuhcNjrqK1V5QQk6EssWWsbp0gGuFRhS5A4A87tJ1XowJqfUbt7Ny41rHuyNteHFa3r3xVOPZfFHEB5IEakuUMpUL9ZvGC", "2024-12-25", "pending");
INSERT INTO BugReport VALUES (NULL, 7, 4, "Bug in 7", "DIpnm0g5pnvTZSY0DZpvFDWwXjD27BCjxMfre9NpYqzGi5dzZUHeY0KJGLRc51yBDh0MMJWFGcUtH6H1mqD6a90tq2obsFFTwqXAtJpOBsiHGiOAb5q1RnkAjZzlvcWRibGbzuDBOpjTn7l6Pq1rWl6GP652ggMkul5IVRTQDBLhwJU0QuiIsCvC3E0TrLbdJZrK0dJgZoHXx3x05ivNptvYWjT4PAE8uuiuJSExHZdVdlXaV2m2qK7KcaDbHGg", "HHHounEKyWl5F8A5gxtL0urazBbq7F0kxZnwm0bPorZQu2NNRAwAmZ4yoXgBybWCoWwVOH0BugPphZldSga7jb6un80iETVeP9tpx7lQ2ylFGVUGgCBQ23Y8SPGlafMjV1D98mp6HZGvtPzglrkXvmTRTnNKRdq7JC2i1uPYhibAxHd1aUwWyh00UlGP0PaBQspJe43WjACrre5CDKPluh06oNLNOU3OyoNqoB4zVNDUOrYAppw7tD0JW6TMPBC", "2023-09-19", "pending");
INSERT INTO BugReport VALUES (NULL, 3, 9, "Bug in 3", "PTxUPqgMfzC9QF1FX362KNqBITf5GaEER5XzuaUi8wErlRJHzNUMJPj5YwTe1rT90GY5WIzQRm6J61DfyvAx74pykZXnOSZCAeA1JhwhK84oKwWEhksZmXnGNSfdwsUtRjxpFrEoseMfyfrMxPJadgHIumPHvnEf91TCgq8urbYvIvfpRiAfcahFe9CbUkV2RY0Oz1roHFXwvI31AetTQViXoAXheSzVWc6OfDEuUPBWfd6tUH9wBrgOMZEPix0", "KhT6rfGDMGqyKNAmnymbidHfu5q0YbwHj2YofJO4yN9CNjYD6PofJOBa3SCMaabOK8mv912p2KRhBgPdxnRAMRllBySZsWxEXEE0mpscijX9y1200gJxBb4AJHdeHDTNhS6Kh3sBdR96O7l1PRCvFsjIfrDE6HHgpfeRDT3cN7YbcFPvjNVBJm6debd0iVCURkTWs1mS4ESdNZsbM3ZM5xAvAjiNgfQEyKaeEOnklFbO5kZqkVsfmFZ19fGZTmH", "2023-02-11", "resolved");
INSERT INTO BugReport VALUES (NULL, 7, 9, "Bug in 7", "ZeLZKUBaryFv8rSsHeBhyPKtUsDhZHZpmqzBe7RomkQnIGt4GZPGxgyANhkGvAwgm4bHU2Ojns3aVmNITMPH7IOXWB1E9v0pdAD1l3aZXTyMBSCGxKR3WG7X3Ckto5mLh4PAHqexjD7woegb8c3sJsOMpqxwx7AVhNFRdUpMSp7P6DtvtvCeR4dffmBxKvofMwiDqnI1beeDk3bIt1unUvAAnLX5jW6pF4XPwNebtGs5G5K94Axpf12aaImDsWO", "85YmFLRBKsLIo2uIiaQqsMkfi83RXOq3Zcg06MdBQQwxwYOcu8y8fBGGPIDB7Q3gArP6LZwR7uDVoWei8upBAlQeXvBveDsoahcKgIGR2YsBdiC0zNIb7k3tlIaJtZuJ6EC1BcMXjla71Y9VIEzYHmMXcSIPXvwDPnqnHJZ9bDiMf4s52eX1Mns53SwEoK0CfKctrmuhUsbg9XnrV3GBEI8J01uas7wzLKBpyGjeJGgA8IlTHOr2y0TflgeT4k2", "2023-02-26", "pending");
INSERT INTO BugReport VALUES (NULL, 8, 1, "Bug in 8", "eRxGbh7PSkwhUsf5v4oBqifzWNvSrUsvlHwBADNYvm2jiGjfhmiHoUzlreuWrYhEnyQ6pv8pllDuNmM8eZqCb2Bwd4N8JXWjz8Hbhw5lbHMdN8HvKUUhFK9bNfHR28D4u6RJ0RPVdV2akdSmzFqqJapOjU3WfPUqaIf0Dg4rv6pR0J8k8BMyihx2jSW12U9ynhIf8FuKAjMg6Zut5xa5Z0DQubo1EdtM226eQmF8KzOiL7ZYTRLYYUkyCkmxUdA", "D8TQq6zduEBEMEOixHikViG9YUj4cwg45Jq3SGqTknPRweKPIQ708YWia9DC6MEQjLTQuiwUkJ02X8o24PIjfePCH9jB2zhB01nYhJUsrxaMh5QWLreDMnYakoghTkjSBcsxRjan1OQtLrdiXJzNfGP5IGHrq8LO2D8jaZajcbCzOyl99EtkU03BJKCMt8lwYQcabiHaAs2914MF2qgvPusBRpzJFFQbJTyOYS4wyDCAVU4mn92iLIpdbHNQ77t", "2023-06-03", "resolved");
INSERT INTO BugReport VALUES (NULL, 9, 2, "Bug in 9", "grata8Q1ZWYvKgPeb63H5LZ8WlA4AC9qDwphWoHG6ARluUxvFUI1JUFuB46DkG4kY31TgAFDUTr1K3i3vGTbXbbdGzCs4MRtQtxn8DGSIeHy8BrbMTVIk4oLICgNCUpd5KMOfDPPRwL5KfUHWNP69zYFnZiSdI9N4ihPQwvTbukY9VOlRWUkPafYEY6c0QuH5SamNnCXMDCChSlrzkUi0VTah5rkSgUGtf9SsdRWqcmSM6vXLMB4XKb9bnF8DZu", "re3KOuC4rdInpju1Y5ZFJfJeHdUEVj9lXOIxD7ySwS0K7pzUTvZKhPu1OeeRlkiOsCgG23hORw3s2V1oe9nlA85TwcSzXWH64HjHHbl9cMae11hoRJpYklGROtG4786U15BLCeb7GqYSqTtKQWE4FMUWdEfYWzh7ZwU9mkwWBdidzbmVzpl0kDHK50M3PFpYCJrkn6b68uH5vTpwClsHGJRRHVUIUfqFT4v8gdUC2gQOe3CfRNVucDWhxW6DJJ8", "2024-01-25", "resolved");
INSERT INTO BugReport VALUES (NULL, 3, 2, "Bug in 3", "IJaOOvlP6MYP53rBRE2aQfh1TnsV05u5U6s8Lp9Fi8GwJS93wQloGSwDh67DXZxl31n1tPlYLyrBSCqQyAahO1IiePFZOut0fiyLeSRooguZG3Ajvmbls9jbAkyRDoqJ3ZFZBzus4F71JIMUbVWV5mKxYmiMRaOtXYhaj0ooNa6KqETI8dQghb6KlEBxO86g115yh7hDnUXnvs9h0zr6ZzTPNWmP82o7mviQdWLHgbuE2WnEGq4dMvMxcIhnUVG", "UvIej0PbDsh4IFgK3uBr5m3b3ixrU87N6r75fT3GXwYY86HPuHNook6mLTGnbei0qRoTvzDwMNyvyZt3QME43GA7ICZqgHg9c0iREgKERMMYI4emcH0ShHy3ndDWgajBFYoEpElKHRNA9PbfIPd4IgfQb1SSzTNH6xAMaIYnCYiIsZfCCjo1q8Dc21HwBvsLZPeecusOf7HtnBayRlkpa7EUEqMHqCIQhs8mXtepJoCJEEQ3MwtJvC3JERJtS5D", "2023-01-26", "resolved");
INSERT INTO BugReport VALUES (NULL, 6, 10, "Bug in 6", "gWYJmVB8lCJdzpT0cm8CJfjq62brYiQTvtGwexWa5M8cKFStb8Xd06uNZEo5mHYbabXJOQhp7RrwMHs4FhbNto5929882JLiXYzfQpk97ENTK6KrJDGkjtHGxXuEsnX4sZELdAcsK7oH5i0eplTauei4olMOwkyY2BuiRbCJ5R5UYVkY82cqsRnWOip9YSQwOYv4yUWovxuM6d4SXtrEThij7SFRMhzmI2A97gfwqJOBEHopQzuSKLCYenNxjHW", "WxzlXFiqjfYMWTnxAkj1HLlnrPKVFVR5H0OT3uug0VDnSs1hK4vMIiTljscxO2KaTMHpYJIcxUqsyG8zX3JuTnREJXZjC0tejseMYVybQMMarprOP9XuW5TyXZrp5wwX4zzFsZTPdd49bqpZNvTMVttFgqPYm7AOQ4TXIHMOIPqghYTAIQQ4gYtRUGIZ0PBWJ9x1U4jOcQ4cy4yzbHox4qGKNpA5neTrr5I68aLRgpJqnX59T7H8utBjVrEThzy", "2023-06-25", "resolved");
INSERT INTO BugReport VALUES (NULL, 2, 6, "Bug in 2", "HOtvkCZ1A1AsSdaw9q805c1dzYGACGuQ5kyEonjQttfj5SlEhPHRGoDdrrrvuPahVKmjIJgjr9slqwyedGfq2iQenJjxgswaP3qSwtzRxJqrwhXYl3JXEX9BSoVxsks1flm61ta5uVrUejGt2i2jFhjRa9ro9MMyJYFGIFe4P2kmS8Jk1Ygu7X0cVV6coZ4rQy5F7SKqYFbJG2gTgTCf9YtVRfsp9NDrvS7hkjpb5ew4XAKm3QMmG0pwUisL4vO", "53RNvZ6VsnFuMeBy1SRLbWQumUhZ3tloWgP1AXpsMJNbjRtfK9oSiw3aVdsenWSnVl9KvSYR6NmtqqszIBvEr0YbbLxWOsUh3Id9upqS4VzEIbBAk0QAVOuJO3EyWN84gsGyAwCG31D7v3HJ7vryhGQtmMacLiXCMGrfQAFwq68Rsap2QoLRVGusnH8ccvEJCktkFNJK6Jz97StDYluWy0ZrCInoGHKqY2J7pAbBkJiSx7NAsKl3Uo02ScOxbVM", "2023-06-05", "resolved");

INSERT INTO BugReport VALUES (NULL, 1, 6, "Bug in 1", "HOtvkCZ1A1AsSdaw9q805c1dzYGACGuQ5kyEonjQttfj5SlEhPHRGoDdrrrvuPahVKmjIJgjr9slqwyedGfq2iQenJjxgswaP3qSwtzRxJqrwhXYl3JXEX9BSoVxsks1flm61ta5uVrUejGt2i2jFhjRa9ro9MMyJYFGIFe4P2kmS8Jk1Ygu7X0cVV6coZ4rQy5F7SKqYFbJG2gTgTCf9YtVRfsp9NDrvS7hkjpb5ew4XAKm3QMmG0pwUisL4vO", "53RNvZ6VsnFuMeBy1SRLbWQumUhZ3tloWgP1AXpsMJNbjRtfK9oSiw3aVdsenWSnVl9KvSYR6NmtqqszIBvEr0YbbLxWOsUh3Id9upqS4VzEIbBAk0QAVOuJO3EyWN84gsGyAwCG31D7v3HJ7vryhGQtmMacLiXCMGrfQAFwq68Rsap2QoLRVGusnH8ccvEJCktkFNJK6Jz97StDYluWy0ZrCInoGHKqY2J7pAbBkJiSx7NAsKl3Uo02ScOxbVM", "2023-11-05", "resolved");
INSERT INTO BugReport VALUES (NULL, 1, 6, "Bug in 1", "HOtvkCZ1A1AsSdaw9q805c1dzYGACGuQ5kyEonjQttfj5SlEhPHRGoDdrrrvuPahVKmjIJgjr9slqwyedGfq2iQenJjxgswaP3qSwtzRxJqrwhXYl3JXEX9BSoVxsks1flm61ta5uVrUejGt2i2jFhjRa9ro9MMyJYFGIFe4P2kmS8Jk1Ygu7X0cVV6coZ4rQy5F7SKqYFbJG2gTgTCf9YtVRfsp9NDrvS7hkjpb5ew4XAKm3QMmG0pwUisL4vO", "53RNvZ6VsnFuMeBy1SRLbWQumUhZ3tloWgP1AXpsMJNbjRtfK9oSiw3aVdsenWSnVl9KvSYR6NmtqqszIBvEr0YbbLxWOsUh3Id9upqS4VzEIbBAk0QAVOuJO3EyWN84gsGyAwCG31D7v3HJ7vryhGQtmMacLiXCMGrfQAFwq68Rsap2QoLRVGusnH8ccvEJCktkFNJK6Jz97StDYluWy0ZrCInoGHKqY2J7pAbBkJiSx7NAsKl3Uo02ScOxbVM", "2023-11-05", "resolved");
INSERT INTO BugReport VALUES (NULL, 1, 6, "Bug in 1", "HOtvkCZ1A1AsSdaw9q805c1dzYGACGuQ5kyEonjQttfj5SlEhPHRGoDdrrrvuPahVKmjIJgjr9slqwyedGfq2iQenJjxgswaP3qSwtzRxJqrwhXYl3JXEX9BSoVxsks1flm61ta5uVrUejGt2i2jFhjRa9ro9MMyJYFGIFe4P2kmS8Jk1Ygu7X0cVV6coZ4rQy5F7SKqYFbJG2gTgTCf9YtVRfsp9NDrvS7hkjpb5ew4XAKm3QMmG0pwUisL4vO", "53RNvZ6VsnFuMeBy1SRLbWQumUhZ3tloWgP1AXpsMJNbjRtfK9oSiw3aVdsenWSnVl9KvSYR6NmtqqszIBvEr0YbbLxWOsUh3Id9upqS4VzEIbBAk0QAVOuJO3EyWN84gsGyAwCG31D7v3HJ7vryhGQtmMacLiXCMGrfQAFwq68Rsap2QoLRVGusnH8ccvEJCktkFNJK6Jz97StDYluWy0ZrCInoGHKqY2J7pAbBkJiSx7NAsKl3Uo02ScOxbVM", "2023-11-05", "resolved");

INSERT INTO AccountChangeRequest VALUES (NULL, 8, "VKZJemCyHqicPAO7JFrrk25sj8GTojiT93FgpPcsdE5s2ufSG9i5q96lt0b1vsxCCTP7qbK5XTwLKjMNUSAM7onbl9aXNO3W8gpg0QnhNvZ2tP3KTlEVIOylJpWApcelbM44g3rrCLR522mk1e1Xj0dwc4LcbJmMBErtJNIeziPSB15hIt0TKQoRY418nuiwTA7tPDvYRawk02Pbu2pEcPrQDKFJG34ksWprPZsgCcRuwmXz60pF9nD3fTEiXeD", "QZgKFJmx55sPmOWVHdQIIkBWCCvSOLWsocHGSbjcgNlfaNqhpmOkcefOGsD8PPOqb0b5L94w24vZc02Kud2id0DLVd6eKxfNgQ3tdWB5mwOXtyXr4QdmNrkNMxjtj3zKJcfHI7OPn4M6KVKGXGfGf7iu6ljssQsysM0gcOakHyUiJ3w3FniwlbI162WBVzz5OE3zmHpwcrSW1vtf6BsG1aU9zjd0GUT5l5VQiR0P9BOhC2ysI0SCGWeqiSRykfc", "2024-05-05", "approved");
INSERT INTO AccountChangeRequest VALUES (NULL, 5, "7EZZF7Jjh7wkqK1xlKFzRqJ1MlqKuIralUUqYHHbyiTISwnChY85sbNb50OQyXdlzOOQJhNhh5PVZMlQLe7kChKkm8DHHcDtEXUjjnAIYNlz7Sv7eEf1j1GNaz0p4JhgQMBjykofJaxayXa3cTLrjAcytqRiUNcFzeqwZcXhzS9Jn3ECw4P22RDy65XFCGBnjQlGpegtXE0VLUf07xeIYDttmNNyqylk1qTIVMcEm9P6CIICbuzAMK4iWu3AkE0", "0RduMKpPL0jazxzCk9qQKXFUOJIFM74afv5ed3MWJhYvn1LAfktaOEVVhB6g68LSZowpYh46mHNR9Jd7AYdpKjlUbdo867uQ5P7IBmgzdO1TpZ0rAmUYM3cQNbqsFTyVmn9scU8dqPq81kmU3E7NGVqIGm66lwPhijLe6uPjXYTyHLat7oHAaHnf5iMgY8FkuLCKcZjv2CB1f5ewEl47QMYsXmwjanki8yWrDyRnCzo5trrn1yvVSBHujcYNbVN", "2024-01-10", "pending");
INSERT INTO AccountChangeRequest VALUES (NULL, 9, "x5rjEEa8fzWGtae8r6Fb7nJvSlKUNEmgdknWW5NBOlmbK5j6AEdoc23LyBIG2Ebu6q94jSpWJ3erh1oyxOni5LqCG2gM7IZCdkDy24R01JPfJjIAo5B5PsET4ADOKKEJCu3QxqR3z8UxDPW56eKHExe5aZIMNC5pAEWtLQMadAlb00QPKhi2KEgvVj80QDN0tpzaFUVlmBstjloWoC6O6Y7RqQSLyCYjryN1Fa8JH2IjnsWTqNPrQ5ONP5X1Fj8", "sFTD29glRFQ1dabaOtUEfYRAauAbe076Z0I1F2emASMPTdSDvAcvkOTUkqgtYfdMUDmmlf3r3TWFAd1v26eR9So68GAhFDDog9Y4goyMFVlp5vSKEDMyHRb4VRWmUqQ6LYw2ys3552InB0SemDRpuioISXNJcnerhpO3S0ii0hys5am62ndXVvJGkaumxzrD54gmxgjg2dMfUZ1nBtwFtwdytaFKJ9aHS19IlUXpEejDiK1eAzQFojmCMeL9EvU", "2024-06-16", "rejected");

