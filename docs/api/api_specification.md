# API Specification

Useful resource: https://learn.microsoft.com/en-us/azure/architecture/best-practices/api-design

## Conventions

1. Values written inside brackets like `{variable}` or `[login]` should **not** be taken literally. An API specification user is expected to insert a real value in their place.

2. The knowledge of the HTTP protocol is assumed.

3. The body of each request should be in the JSON format.

4. Primary keys should be omitted for requests using the POST method if not specified otherwise.

5. All attributes of an entity, except for primary key, should be in the **body** of a request.

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

## /api/account_change_request

- URI: /api/auth/{request_id}
- Allowed methods: POST, GET, PUT, DELETE
- omit `request_id` for a request using POST method
- PUT method requires: `description`, `review_status`, `user_id` in the request **body** and  `request_id` in request **header**
- POST method requires: `description`, `user_id` in the request **body**
- Authorised users: administrator, concerned client (GET, POST)

##  Other endpoints

All other endpoints should be treated according to the rules specified in the `Conventions` section. 

## Authentication

The preferred method is to include the access token in the `Authorization` header using the *Bearer* scheme.

Example:

```http
GET /api/user/10 HTTP/1.1
Host: softwarestore.edu.pl
Authorization: Bearer [your_access_token]
```

The scheme is the following:

![api_authentication_model](/home/igor/software-store/docs/api/api_authentication_model.png)