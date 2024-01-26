# Backend Todo List
To finish the backend, do all the tasks below:

- [ ] Login and password in body instead in path (?)

## Fuctional requirements
- [x] Unregisted user may sign up.
- [x] Client may search the repository for software units.
- [x] Client may get detalied information about a given software unit.
- [x] Client may download an executable.
- [x] Client may post a review.
- [x] Client may edit their own review.
- [x] Client may remove their own review.
- [x] Client may post a rating for a software unit.
- [x] Client may remove their own rating for a software unit.
- [x] Client may browse reviews.
- [x] Client may see average raiting of a software unit.
- [x] Client may report a software bug.
- [x] Client may report a statute violation by a software unit.
- [x] Client may request a change to their account type from Client to Software Author.
- [x] Software author may add new software units.
- [x] Software author may modify their own software units.
- [x] Software author may read the bug reports regarding their software units.
- [x] Software author may remove the bug reports regarding their software units.
- [x] Administrator may accept/reject clients' account change requests.
- [x] Administrator may browse statute violation reports.
- [x] Administrator may unblock/block software units.
- [x] Administrator may remove software units.
- [x] Administrator may remove any review.
- [x] Administrator may remove any account that belongs to a software author or client.
- [x] Administrator may download source code of any software unit.

## Bugs
- [x] Make a bearer token expire after the time specified in Config.
- [ ] SoftwareUnit can't be searched by categories (implement View??).
- [ ] Can't delete SoftwareVersion, Integrity constraint violation: 1451 Cannot delete or update a parent row: a foreign key constraint fails (`software_store`.`Executable`, CONSTRAINT `fk_Executable_SoftwareVersion` FOREIGN KEY (`version_id`) REFERENCES `SoftwareVersion` (`version_id`))  (use procedure ??)
- [ ] Change date format in SQL in user date (time is not important)
- [ ] Client can't create account change request (wrong endpoint)
- [ ] Client can't update account (he should can update username ?)
- [ ] Updating User can't be done if there is not matched user_id with login. If only Admin can do update, is that necessary?
- [ ] Account change request update
- [x] AVG and Count Average dont work

