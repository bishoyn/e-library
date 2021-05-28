# e-library API doc

## User API

#### Base URL ``` https://joberapp.net/e-library/api ```

### Rigester
```JSON
POST /user/register.php

Headers
Content-Type:application/json

Example Body
{
    "firstname":"Mohamed",   
    "lastname":"Ahmed",
    "email":"mohamed@gmail.com",
    "password":123456789
}

Example Response
{
    "success": true,
    "userdata": {
        "id": 4,
        "email": "ahme@gmail.com",
        "firstname": "ahmen",
        "lastname": "mostafa",
        "balance": 0
    }
}
```

### Login
```JSON
POST /user/login.php

Headers
Content-Type:application/json

Example Body
{
    "email":"ahme@gmail.com",
    "password":123456789
}

Example Response
{
    "success": true,
    "userdata": {
        "id": "4",
        "email": "ahme@gmail.com",
        "first_name": "ahmen",
        "last_name": "mostafa",
        "balance": "0.00"
    }
}
```

### Buy Books
```JSON
POST /user/register_books.php

Headers
Content-Type:application/json

Example Body
{
    "user_id":4,
    "books": ["000636988X", "0060094850"]
}

Example Response
{
    "success": true,
    "message": "user successfully purchased all books"
}
```

### Get User Books
```JSON
GET /user/books.php?id=1

Headers
Content-Type:application/json

Parameters
id


Example Response
{
    "success": true,
    "user_id": "1",
    "books": [
        {
            "book_id": "0002005018",
            "purchased_date": "2021-04-25 19:49:54",
            "title": "Clara Callan: A novel",
            "category_name": "Women Teachers",
            "auther_name": "Wright, Richard Bruce",
            "language": "en_US",
            "pages": "414",
            "image": "assets/images/0002005018.01.LZZZZZZZ.jpg",
            "price": "30.00"
        },
        {
            "book_id": "000636988X",
            "purchased_date": "2021-04-25 19:55:19",
            "title": "How Not to Be a Perfect Mother : The Crafty Mother's Guide to a Quiet Life",
            "category_name": "Family & Relationships",
            "auther_name": "Purves, Libby",
            "language": "en_US",
            "pages": "192",
            "image": "assets/images/000636988X.01.LZZZZZZZ.jpg",
            "price": "10.00"
        }
    ]
}
```

### User Rate a Book
```JSON
POST /user/rate_book.php

Headers
Content-Type:application/json

Example Body
{
    "user_id":4,
    "book_id":"000636988X",
    "rate":5
}

Notes "rate" property must be between 1-5
 
Example Response
{
    "success": true,
    "message": "user successfully rated book: 000636988X"
}
```

### User Add funds
```JSON
POST /user/funds.php

Headers
Content-Type:application/json

Example Body
{
    "user_id":4,
    "amount": 30
}
 
Example Response
{
    "success": true,
    "message": "funds added successfully"
}
```

### Get User Balance
```JSON
GET /user/get_balance.php?id=1

Headers
Content-Type:application/json

Parameters
id


Example Response
{
    "success": true,
    "user_id": "1",
    "balance": "2887.00"
}
```

## Book API

### Get All Books
```JSON
GET /book/getall.php?limit=2

Headers
Content-Type:application/json

Parameters
limit

Note: limit parameter is not required if will not pass it you will get all the books

Example Response
{
    "success": true,
    "books": [
        {
            "id": "0002005018",
            "title": "Clara Callan: A novel",
            "category_name": "Women Teachers",
            "auther_name": "Wright, Richard Bruce",
            "language": "en_US",
            "pages": "414",
            "image": "assets/images/0002005018.01.LZZZZZZZ.jpg",
            "price": "30.00"
        },
        {
            "id": "000636988X",
            "title": "How Not to Be a Perfect Mother : The Crafty Mother's Guide to a Quiet Life",
            "category_name": "Family & Relationships",
            "auther_name": "Purves, Libby",
            "language": "en_US",
            "pages": "192",
            "image": "assets/images/000636988X.01.LZZZZZZZ.jpg",
            "price": "10.00"
        }
    ]
}
```

### Get Book By Id
```JSON
GET /book/book.php?id=000649840X

Headers
Content-Type:application/json

Parameters
id

Example Response
{
    "success": true,
    "bookdata": {
        "id": "000649840X",
        "title": "Angela's Ashes: A Memoir",
        "category_name": "Biographies & Autobiographies",
        "auther_name": "McCourt, Frank",
        "language": "en_US",
        "pages": "368",
        "image": "assets/images/5.jpeg",
        "price": "100.00"
    }
}
```

### Get Book Rating
```JSON
GET /book/rating.php?id=000636988X

Headers
Content-Type:application/json

Parameters
id

Example Response
{
    "success": true,
    "books": [
        {
            "user_id": "1",
            "book_id": "000636988X",
            "rate": "3"
        },
        {
            "user_id": "3",
            "book_id": "000636988X",
            "rate": "5"
        },
        {
            "user_id": "4",
            "book_id": "000636988X",
            "rate": "5"
        }
    ]
}
```
