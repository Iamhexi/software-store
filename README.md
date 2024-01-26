# Software Repository
Software Repository is the RESTful Web API, which offers fuctionality of a software repository. 

# Features
- adding software (in multiple versions, handles semantic versioning),
- downloading software,
- registration and singing in,
- strong authentication and flexible authorization (with token bearer schema and privileges system), 
- posting a review,
- leaving a rating,
- managing registered users as an administrator (all CRUD operations are available, search has search extensive options),
- reporting if it contains a bug or violates a statute,
- on-demand software compilation on server-side (users download ready-made executables, software authors upload source code),
- well-documented (however, mostly in Polish),
- customisable and extendable.

# Requirements
- PHP 8.0+ with the PDO extension,
- MariaDB as a database management system (DBMS).

# For whom
For people who want to host their own software repository. For privacy-minded people as the project is fully open-source. For front-end developers as the project is lacking any user interface (UI).

## For contributors
1. Create your own branch from `main` branch.
2. Develop the feature you want to.
3. Write unit tests and conduct manual testing (we used Postman but you may use whatever you like). All the tests have to pass.
4. Merge and send us a pull request.
5. Done ;)
