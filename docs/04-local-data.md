# Local Data

Feel free to document local data you add.

We took the liberty of creating an initial Seeder with:
1. User for you to login:
   1. email: sales@coffee.shop
   2. password: password

Run the seeder to include product options for the dropdown:
```bash
php artisan db:seed
```
For the money package the following .env variable with need to be included:
```bash
MONEY_DEFAULTS_CURRENCY=GBP
```
