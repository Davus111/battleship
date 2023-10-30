## Battleships

Your assignment is to create a API backend for a battleship(https://en.wikipedia.org/wiki/Battleship_(game)) game.

#### The game should include:
1. Creating a new battle ship game. The game board should be a 10x10 grid and each player should have:
    - 1 ship taking 3 fields
    - 2 ships taking 2 fields
    - 4 ships taking 1 fields
   
2. Browsing currently opened/available games.
3. Joining a game. A player can join any game, unless they are the creator.
4. After both players have joined the game, they should be able to set up their ships on the board.
5. When all ships are set, the game starts. Players can attack each other's fields once per turn. However, if a player hits a ship, they can continue attacking until they miss.
6. If a player shoots a field that has already been targeted, they can shoot again.
7. Once one of the players manages to destroy all of the opponent's ships, they should receive a message about winning the game.
8. You can skip the functionality of notifying players about the state of their ships. Same goes for the player that lost.

#### In order to run the application, follow the steps below:

1. Install the dependencies:

    ```bash
    composer install
    ```
2. Create a SQLite database:

    ```bash
    touch database/database.sqlite
    ```
3. Run the database migrations:

    ```bash
    php artisan migrate
    ```
4. Create a user:

    ```bash
    php artisan db:seed DatabaseSeeder
    ```
5. Serve the application:

    ```bash
    php artisan serve
    ```

In order to authorize API requests, you need to use the token provided in the `users` table. Include the token as an HTTP header with each API request.

The format of the header should be:

```bash
Authorization: Bearer <your_token>
```

We except your code to be clean and solution well thought. 
You can skip tests, writing a documentation, filtering/sorting on GET endpoints, logging etc.
For simplicity, please use provided auth method.
If something weird happens with SQLite and it won't work, go with mysql.

If you have any questions, please, feel free to contact us and ask! :)

Once you are ready with assignment, make a new repository and send a link to us. 

Good luck!
