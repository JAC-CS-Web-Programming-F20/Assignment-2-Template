# Assignment 2 - Routing & Controllers

- 💯**Worth**: 7.5%
- 📅**Due**: October 18, 2020 @ 23:59
- 🙅🏽‍**Penalty**: Late submissions lose 10% per day to a maximum of 3 days. Nothing is accepted after 3 days and a grade of 0% will be given.

## 🎯 Objectives

- **Transact** data to and from a database using models.
- **Interface** between the client and models using controllers.
- **Route** to the proper controller based on the HTTP method and resource requested.

## 📥 Submission

Since we'll be using [Git](https://git-scm.com/) and [GitHub Classroom](https://classroom.github.com/) for this assignment, all you need to do to submit is to commit and push your code to the repository. Over the course of working on the assignment, you should be committing and pushing as you go. I will simply grade the last commit that was made before the assignment deadline.

## 🔨 Setup

1. Attach VSCode to the PHP container. Make sure that inside the container, you're currently in `/var/www/html/Assignments`.
2. Follow the instructions from A1 to clone the repo if you're not sure how.
3. You should now have a folder inside `Assignments` called `web-programming-f20-assignment-2-githubusername`.
4. Inside of the newly cloned repo, copy all your models from A1 into `src/Models`.

## 🖋️ Description

In A1, we created the 4 main models (`User`, `Category`, `Post`, and `Comment`) that are in charge of talking to the database. The models are based on the entities from the ERD which can be found in the A1 specs. In this assignment, we will implement the following:

1. The `Router` which handles the web requests/responses and instantiates a `Controller`.
2. The `Controller` which decides what `Model` method to call.

## 🗺️ Routes

| HTTP Method | Route            | Controller | Method    |
| ----------- | ---------------- | ---------- | --------- |
| `GET`       | `/`              | Home       |           |
| `ANY`       | `/{garbage}`     | Error      |           |
| `GET`       | `/user/{id}`     | User       | `show`    |
| `POST`      | `/user`          | User       | `new`     |
| `PUT`       | `/user/{id}`     | User       | `edit`    |
| `DELETE`    | `/user/{id}`     | User       | `destroy` |
| `GET`       | `/category/{id}` | Category   | `show`    |
| `POST`      | `/category`      | Category   | `new`     |
| `PUT`       | `/category/{id}` | Category   | `edit`    |
| `DELETE`    | `/category/{id}` | Category   | `destroy` |
| `GET`       | `/post/{id}`     | Post       | `show`    |
| `POST`      | `/post`          | Post       | `new`     |
| `PUT`       | `/post/{id}`     | Post       | `edit`    |
| `DELETE`    | `/post/{id}`     | Post       | `destroy` |
| `GET`       | `/comment/{id}`  | Comment    | `show`    |
| `POST`      | `/comment`       | Comment    | `new`     |
| `PUT`       | `/comment/{id}`  | Comment    | `edit`    |
| `DELETE`    | `/comment/{id}`  | Comment    | `destroy` |

## 🧪 Tests

Inside of the `tests` folder you will find three subfolders of test suites.

1. `ModelTests` are the tests from A1. These tests call methods directly on the models to see if the correct data is being transacted to/from the database.
2. `ControllerTests` are the tests that will invoke methods on the controllers to see if the right models are being called.
3. `RouterTests` will simulate a web client using Guzzle to test if your router is:
   1. Parsing the query string correctly
   2. Calling the correct controller
   3. Sending the correct response back to the client

![Tests](Tests.png)

You should develop your application in this order. Verify that the models are working from last assignment and make any adjustments as necessary (ex. rename all your `delete` model methods to `remove`). Then move on to creating the controllers and running the corresponding test suite. Finally, create the router and run the last set of tests to verify that everything is working as it should.

I **highly** recommend you get into the habit of running one test/suite at a time as it will make your life a lot easier while working. If you're unsure of how to do that, refer back to E2.3.
