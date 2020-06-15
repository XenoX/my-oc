# My OC

My OC is a local app (build with symfony) to help mentors.

![image](https://user-images.githubusercontent.com/1884209/84604008-5eac5800-ae93-11ea-942f-b73a818e6544.png)

## Requirements

- PHP 7.2.5 or higher
- SQL Database
- [Composer](https://getcomposer.org/)

## Installation

#### Clone or download this repository

```bash
$ git clone git@github.com:XenoX/my-oc.git
```

#### Install dependencies

```bash
# in project directory
$ composer install
```

#### Set env vars

 ```bash
 $ cp .env .env.local
 ```

Go to `.env.local` file and set :

```bash
DATABASE_URL # Generally change only db_user, db_password and db_name
OAUTH_OC_CLIENT_ID # On OC website (logged), type App.api.anonymous.client_id on console
OAUTH_OC_CLIENT_SECRET # On OC website (logged), type App.api.anonymous.client_secret on console
```

#### Set up database

```bash
$ php bin/console doctrine:database:create
$ php bin/console doctrine:migrations:migrate
```

#### Launch server

❗[Symfony binary](https://symfony.com/download) required

```bash
$ symfony server:start
```


## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Don't forget to use [Gitmoji](https://gitmoji.carloscuesta.me/) in your commit name.

Please make sure to update tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)