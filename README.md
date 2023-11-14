# kanye-west-quotes

## How to test it

- Clone the repository
```sh
git clone https://github.com/mrbadguy78/kanye-west-quotes.git
```

- Enter the kanye-west-quotes directory
```sh
cd kanye-west-quotes
```

- Create and start the container in detached mode
```sh
docker-compose up -d
```

- Run composer install
```sh
docker exec -it kanye-west-quotes composer install
```
- Run the tests
```sh
docker exec -it kanye-west-quotes ./vendor/bin/pest
```
