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
- /api/software, api/software/{softwareId}/rating, api/software/{softwareId}/rating/average, api/software/{softwareId}/rating/count
- /api/auth
- /api/user/{userId}/account_change_request
- /api/review
- /api/rating
- /api/bug_report
- /api/statute_violation_report
- /api/software_version
- /api/download
- /api/source_code

## /api/user/{userId}
- Summary: Allows CRUD operations on the user with the provided `userId`.
- Allowed methods: POST, GET, PUT, DELETE
- Required privileges: administrator, owner of the account

## /api/user/{userId}
- Summary: Gets all users available.
- Allowed methods: GET
- Authorised users: administrator

## /api/auth
- Summary: After providing `login` and `password` correct query parameters, the bearer token is returned.
- Allowed methods: POST
- Query parameters (request header): login=[login]&password=[user_password]

- Response body (in case of a success): 

  ```json
  {
    "code" : 200,
    "message" : "Success",
  	"data" : "[your_token]"
  }
  ```

- Authorised users: everybody (**including unregistered users**)

## /api/user/{userId}/account_change_request
- Summary: Manages account type change request connected with the specific user.
- Allowed methods: POST, GET, PUT, DELETE
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
