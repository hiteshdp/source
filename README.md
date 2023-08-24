
## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 1500 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).
## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Installation

Clone the repository

    git clone http://git1.webline.local/PHP/php_wellkasa.git

Switch to the repo folder

    cd source

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Generate a new application key

    php artisan key:generate


Run the database migrations (**Set the database connection in .env before migrating**)

    php artisan migrate

Generate a new passport authentication secret key

    composer require laravel/passport

    php artisan migrate

    php artisan passport:install


**General command list**

    git clone http://git1.webline.local/PHP/php_wellkasa.git
    cd source
    composer install
    cp .env.example .env
    php artisan key:generate
    php artisan migrate
    composer require laravel/passport
    php artisan migrate
    php artisan passport:install
    
- `php artisan down` -> This command will put the maintenance mode screen if any page is accessed but won't bypass any functionality.
- `php artisan up` -> This command will bring the website back online and functionality to access everything.
## Database Set up

- Access the database using UI access or commandline and export it to some directory.
- Create new database on new database server.
- Import exported Wellkasa database to new database so all tables will be there.
- Update .env configuration file with database credentials.

**Make sure you set the correct database connection information before running the migrations** [Environment variables](#environment-variables)

    php artisan migrate
## Git process 

- `master` Branch - It contains all main code developement files which presents to the [Live environment](https://wellkasa.app/home/).
- `developer` Branch - It contains all code developement files which presents to the [Stage environment](https://stage.wellkasa.app/home/).
- `dev` Branch - It contains all code developement files which presents to [development environment](https://dev.wellkasa.app/home/).

- Every changes of  deployment starts from the development (dev branch) after its merged from dev to stage (developer branch) and finally when merged from stage to live (master branch) it presents to Live user website.

- The deployment process defined to which server and what to deploy is mentioned in the `gitlab-ci.yml` file in the root of the branch of each (master, developer, dev) branch.
## Github CICD Set up

- created appspec.yml to define deployment on server.
- Inside scripts directory on root of repository create install_dependencies.sh and deploy_laravel.sh files which will perform build task on server.
- In AWS, Create Code pipeline for each server where we need to select github as SCM and add github credetials to connect.
- Select repository branch for ci/cd.
- Create Application and deployment group in AWS Code Deploy
- After completing Code pipeline once you push code it will be deployed on server.

## API Specification

- `TRC APIs` - TRC APIs are integrated through cronjobs which are in `app/Console/Commands` directory. API URLs and keys can be configured in .env configuration file.

## Folders
### Laravel (source folder)
- `app` - Contains all the Eloquent models
- `app/Http/Controllers/Api` - Contains all the api controllers
- `app/Http/Middleware` - Contains the auth middleware
- `config` - Contains all the application configuration files
- `database/factories` - Contains the model factory for all the models
- `database/migrations` - Contains all the database migrations
- `database/seeds` - Contains the database seeder
- `routes/api` - Contains all the api routes defined in api.php file
- `routes/web` - Contains all the web (frontend) routes defined in web.php file
### Laravel Mix - To combine assets like css and js
- Laravel Mix makes it a cinch to compile and minify your application's CSS and JavaScript files. Through simple method chaining, you can fluently define your asset pipeline.

- Run all Mix tasks...

    npm run dev
 
- Run all Mix tasks and minify output...
    
    npm run prod

### Execute Laravel Code
- Use below command under `source/` folder
    
        php artisan serve
- Above command will start laravel project running
- Use the URL from the terminal and paste it in the web browser

### Angular Admin (admin folder)
- `admin/src/app` - Contains all logic code for the frontend pages.
- `admin/src/assets` - Contains images for the webpages to display.
- `admin/src/environment` - Contains the variables for the API endpoints with the frontend to connect backend.
### Setup Angular (admin folder)
- Step 1 : Install node_modules using below command under admin folder

        npm install

- Step 2 : Set the URL of laravel in enviornment.ts file under this path -> `admin/src/environments/environment.ts`

- Step 3 : Execute below command under `admin/src` folder

        ng serve
## Third party libraries

- `Socialite` - Laravel also provides a simple, convenient way to authenticate with OAuth providers using Laravel Socialite. We have used currently authentication via Facebook, Google in Sign up / Registeration. 
- All its credentials to connect with its social media will goes to .env files 

## Environment variables

- `.env` - Environment variables can be set in this file

***Note*** : You can quickly set the database information and other variables in this file and have the application fully working.
# Authentication
 
This applications uses OAuth Tiken to handle authentication. The token is passed with each request using the `Authorization` header with `Token` scheme. The authentication middleware handles the validation and authentication of the token. Please check the following sources to learn more about Passport.
 
- https://laravel.com/docs/8.x/passport

# Payment gateway
Laravel Cashier Stripe provides an expressive, fluent interface to Stripe's subscription billing services. It handles almost all of the boilerplate subscription billing code you are dreading writing. In addition to basic subscription management, Cashier can handle coupons, swapping subscription, subscription "quantities", cancellation grace periods, and even generate invoice PDFs.

Below are step for install and configure Cashier Stripe package
Step 1: Installation
- composer require laravel/cashier

Step 2: Database Migrations
- php artisan migrate
- php artisan vendor:publish --tag="cashier-migrations"

Step 3: You should configure your Stripe API keys in your application's .env file.
- STRIPE_KEY=your-stripe-key
- STRIPE_SECRET=your-stripe-secret
- CASHIER_CURRENCY=usd
- CASHIER_ENV=testing

Step 4: Make route and create controller for add subscription logic

`Webhooks`
- Step 1: You should to set webhooks on stripe dashboard with `stripe/webhook` end points.
- Step 2: Register webhook event in `EventServiceProvider`.
- Step 3: Create `Events Listener` and add webhook logic in handle method of listener.


# TRC Interaction Checker Automation
- Automation test function to verify/check interaction data from csv file to database data and generate report with pass/fail status with fail reason if any records get fail with any reason from different scenarios.

- Below are csv and logic files URLs
Logic file path : /var/www/dev/app/Console/Commands/InteractionCheckerAutomation.php
Import path : /var/www/dev/public/import/interactions.csv
Result path : /var/www/dev/public/import/interaction_checker_output/result.csv

- Below are command for run console command 
`php artisan interactionChecker:automation`

Once process is complated then generate result.csv resport in public `/var/www/dev/public/import/interaction_checker_output` directory


# Wellkasa TRC CRON
### 1. Add / Update therapy details
- Command
    ```
    php artisan therapy:details
- Description
    * This command will ensure all the therapy effectiveness and its details are added/updated.  
    * Added therapies from therapy table data will be picked up and will use its apiID as a reference to fetch the details from the TRC API and add in the therapy_details table if not inserted or will be updated and will add the logs accordingly in the logs table. 
- CRON Time Interval
    * After every 2 minutes  
    * Processing 5 therapy at a time so 300 therapies will be processed in 1 hour 
    * So total around 1500 therapies will be processed in 5 hours.

### 2. Add / Update therapy reference details
- Command
    ```
    php artisan therapy:reference
- Description
    * This command will ensure all the therapy reference details are added/updated.  
    * Added therapies from therapy table data will be picked up and will use its apiID as a reference to fetch the details from the TRC API and add in the therapy_reference table if not inserted or will be updated and will add the logs accordingly in the logs table.  
- CRON Time Interval
    * After every 3 minutes 
    * Processing 1 therapy at a time so 20 therapies will be processed in 1 hour 
    * So total around 1500 therapies will be processed in 3 days. 


### 3. Add / Update therapy condition details
- Command
    ```
    php artisan therapy: condition-import
- Description
    * This command will ensure all the conditions of the therapies are added/updated. 
    * Fetches the therapy data from therapy_details table and get the conditions from the effectiveDetail column and extract condition name from it and add in the condition table and add its therapy mapping details in the therapy_condition table   
- CRON Time Interval
    * After every 4 minutes 
    * Processing 1 therapy at a time so 15 therapies will be processed in 1 hour 
    * So total around 1500 therapies will be processed in 4 days. 

# Pre Deployment process
- Make build of specific branch as below
    * dev branch then in enviornment.prod.ts change domain URL to 'https://dev.wellkasa.app/'
    * developer branch then in enviornment.prod.ts change domain URL to 'https://stage.wellkasa.app/'
    * live branch then in enviornment.prod.ts change domain URL to 'https://wellkasa.app/'
  
- Command to make admin build
    * Run below command in terminal under admin/src/ folder
        ```
        ng build --prod --aot
- After build done
    - change `<base href="/">` to  `<base href="https://{domain}/admin/">` in index.html of dist folder
    - Copy all the files from the dist ('admin/dist') folder to laravel public admin folder ('source/public/admin')

# Post Deployment process

- For Live deployment process 
    - Make live admin build and copy to laravel public admin ('source/public/admin') folder
    - Merge Feature branch to dev
    - Merge dev branch to developer 
    - Merge developer branch to live
 
- For Stage deployment process 
    - Make stage admin build and copy to laravel public admin ('source/public/admin') folder
    - Merge Feature branch to dev
    - Merge dev branch to developer 

- For Dev deployment process 
    - Make dev admin build and copy to laravel public admin ('source/public/admin') folder
    - Merge Feature branch to dev

# AWS Database and Instance Backups

- Database backup configured on daily basis with retention of 35 days. 
- AWS back up everyday and will keep last 7 days backup. 
- It will cost you around $7-10  per month.