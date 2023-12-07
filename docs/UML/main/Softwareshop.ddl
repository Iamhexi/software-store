CREATE TABLE `User` (
  user_id               int(10) NOT NULL AUTO_INCREMENT, 
  login                 varchar(255) NOT NULL, 
  pass_hash             binary(128) NOT NULL, 
  username              varchar(255), 
  account_creation_date date, 
  account_type          varchar(6) NOT NULL, 
  PRIMARY KEY (user_id));
CREATE TABLE Review (
  review_id         int(10) NOT NULL AUTO_INCREMENT, 
  author_id         int(10) NOT NULL, 
  software_id       int(10) NOT NULL, 
  title             varchar(255), 
  description       varchar(255), 
  date_added        date NOT NULL, 
  date_last_updated date NOT NULL, 
  PRIMARY KEY (review_id));
CREATE TABLE Rating (
  rating_id   int(10) NOT NULL AUTO_INCREMENT, 
  author_id   int(10) NOT NULL, 
  software_id int(10) NOT NULL, 
  mark        tinyint NOT NULL, 
  date_added  date NOT NULL, 
  PRIMARY KEY (rating_id));
CREATE TABLE BugReport (
  report_id                       int(10) NOT NULL AUTO_INCREMENT, 
  software_id                     int(10) NOT NULL, 
  user_id                         int(10) NOT NULL, 
  title                           varchar(255), 
  description_of_steps_to_get_bug varchar(255), 
  bug_description                 varchar(255), 
  date_added                      date NOT NULL, 
  review_status                   varchar(20) NOT NULL, 
  PRIMARY KEY (report_id));
CREATE TABLE StatuteViolationReport (
  report_id     int(10) NOT NULL AUTO_INCREMENT, 
  software_id   int(10) NOT NULL, 
  user_id       int(10) NOT NULL, 
  rule_point    int(10) NOT NULL, 
  description   varchar(255), 
  date_added    date NOT NULL, 
  review_status varchar(20) NOT NULL, 
  PRIMARY KEY (report_id));
CREATE TABLE AccountChangeRequest (
  request_id     int(10) NOT NULL AUTO_INCREMENT, 
  user_id        int(10) NOT NULL, 
  description    varchar(255), 
  date_submitted date NOT NULL, 
  review_status  varchar(20) NOT NULL, 
  PRIMARY KEY (request_id));
CREATE TABLE SoftwareUnit (
  software_id     int(10) NOT NULL AUTO_INCREMENT, 
  author_id       int(10) NOT NULL, 
  name            varchar(255) NOT NULL, 
  description     varchar(255), 
  link_to_graphic varchar(255), 
  is_blocked      tinyint(1) NOT NULL, 
  PRIMARY KEY (software_id));
CREATE TABLE SoftwareVersion (
  version_id    int(10) NOT NULL AUTO_INCREMENT, 
  software_id   int(10) NOT NULL, 
  description   varchar(255), 
  date_added    date NOT NULL, 
  major_version int(10) NOT NULL, 
  minor_version int(10) NOT NULL, 
  patch_version varchar(255) NOT NULL, 
  PRIMARY KEY (version_id));
CREATE TABLE SourceCode (
  code_id    int(10) NOT NULL AUTO_INCREMENT, 
  version_id int(10) NOT NULL, 
  filepath   varchar(255) NOT NULL, 
  PRIMARY KEY (code_id));
CREATE TABLE Executable (
  executable_id       int(10) NOT NULL AUTO_INCREMENT, 
  version_id          int(10) NOT NULL, 
  target_architecture varchar(20) NOT NULL, 
  date_compiled       date NOT NULL, 
  filepath            varchar(255) NOT NULL, 
  PRIMARY KEY (executable_id));
CREATE TABLE Download (
  download_id   int(10) NOT NULL AUTO_INCREMENT, 
  user_id       int(10) NOT NULL, 
  executable_id int(10) NOT NULL, 
  date_download timestamp NOT NULL, 
  PRIMARY KEY (download_id));
CREATE TABLE Category (
  category_id int(10) NOT NULL UNIQUE, 
  name        varchar(100) NOT NULL, 
  description varchar(255) NOT NULL);
CREATE TABLE SoftwareCategory (
  software_id int(10) NOT NULL, 
  category_id int(10) NOT NULL);
ALTER TABLE SoftwareCategory ADD CONSTRAINT Associates FOREIGN KEY (category_id) REFERENCES Category (category_id);
ALTER TABLE SoftwareCategory ADD CONSTRAINT Connects FOREIGN KEY (software_id) REFERENCES SoftwareUnit (software_id);
