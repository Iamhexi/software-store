# API Specification

Useful resource: https://learn.microsoft.com/en-us/azure/architecture/best-practices/api-design

## Conventions

1. Values written inside brackets like `{variable}` or `[login]` should **not** be taken literally. An API specification user is expected to insert a real value in their place.

2. The knowledge of the HTTP protocol is assumed.

## Endpoint list

- /api/user
- /api/users

- /api/software

- /api/auth

- /api/account_change_request
- /api/review
- /api/rating
- /api/bug_report
- /api/statute_violation_report
- /api/software_version
- /api/download
- /api/source_code

## /api/user

- URI: /api/user/{user_id}
- Allowed methods: POST, GET, PUT, DELETE
- Required privileges: administrator, owner of the account

## /api/users

- URI: /api/users
- Allowed methods: GET
- Authorised users: administrator

## /api/auth

- URI: /api/auth

- Allowed methods: POST

- Request body:

  ```json
  {
  	"login" : "[login]",
  	"hashed_password" : "[hashed_password]"
  }
  ```

- Response body (in case of a success): 

  ```json
  {
  	"access_token" : "[your_token]"
  }
  ```

- Authorised users: everybody (**including unregistered users**)

## Authentication

The preferred method is to include the access token in the `Authorization` header using the *Bearer* scheme.

Example:

```http
GET /api/user/10 HTTP/1.1
Host: softwarestore.edu.pl
Authorization: Bearer [your_access_token]
```

The scheme is the follwoing:

![api_authentication_model](/home/igor/software-store/docs/api/api_authentication_model.png)