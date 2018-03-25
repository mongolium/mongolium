# Mongolium Core
[![Build Status](https://travis-ci.org/mongolium/mongolium.svg?branch=master)](https://travis-ci.org/mongolium/mongolium) [![codecov](https://codecov.io/gh/mongolium/mongolium/branch/master/graph/badge.svg)](https://codecov.io/gh/mongolium/mongolium)
[![StyleCI](https://styleci.io/repos/123986051/shield?branch=master)](https://styleci.io/repos/123986051)

A PHP and MongoDB based CMS for developers that delivers data via APIs and services only.

## Installation

## Overview

Mongolium is composed of four layers.

- **Controllers:** manage restful requests, interacts with the services layer and returns JSON for the API.
- **Services:** core business logic, provides application interfaces to access and process all data.
- **Models:** strongly defined value objects / entities that provide structure to the Mongo objects and collections.
- **ORM:** responsible for hydrating the model entities with data returned from Mongo before passing them back to the services layer.

## API Endpoints

All API endpoints sit under the namespace `api`.

```
POST http://localhost/api/token // Create a JWT token [basic authentication required]
PATCH http://localhost/api/token // Update a JWT token [authentication required]

GET http://localhost/api/admins // Get a list of admins [authentication required]
GET http://localhost/api/admins/{id} // Get individual admin [authentication required]
POST http://localhost/api/admins // Create admin [authentication required]
PATCH http://localhost/api/admins // Update admin [authentication required]
DELETE http://localhost/api/admins/{id} // Delete individual admin [authentication required]

GET http://localhost/api/posts // Get a list of posts
GET http://localhost/api/posts/{id} // Get individual post
POST http://localhost/api/posts // Create post [authentication required]
PATCH http://localhost/api/posts // Update post [authentication required]
DELETE http://localhost/api/posts/{id} // Delete individual post [authentication required]

GET http://localhost/api/pages // Get a list of pages
GET http://localhost/api/pages/{id} // Get individual page
POST http://localhost/api/pages // Create page [authentication required]
PATCH http://localhost/api/pages // Update page [authentication required]
DELETE http://localhost/api/pages/{id} // Delete individual page [authentication required]
```

## API Authentication

All API authentication requires you to use the authorization header which is processed by middleware placed on the relevant routes.

- **Basic:** send a base65 encoded username and password separated by a colon via the basic authorisation protocol. This type of authentication is only used to generate a new JWT token.
- **Bearer:** send a JSON Web Token as a bearer token in the authorization header.

## Extension  

Mongolium is setup to provide some basic CMS functionality out of the box. It is also open to extension and development in any way you see fit.

It is advised that rather than editing the core files you place your extensions along with your new controllers, services and models in the `src/App` directory. 

## Author

Rob Waller [@RobDWaller](https://twitter.com/RobDWaller)
