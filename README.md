# Profile Matcher

> Laravel application for matching user profiles to real estate properties.


**Acceptance criteria:**
- microservice should have only one GET endpoint "`api/match/{propertyId}`"
- Response should have the following structure:

```php
"data" => [
	["searchProfileId" => {id}, "score" => {matchScore}, "strictMatchesCount" => {counter},"looseMatchesCount" => {counter}],
	["searchProfileId" => {id}, "score" => {matchScore}, "strictMatchesCount" => {counter},"looseMatchesCount" => {counter}],
	["searchProfileId" => {id}, "score" => {matchScore}, "strictMatchesCount" => {counter},"looseMatchesCount" => {counter}],
	[...],
	[...],
	[...]
]
```

- The response should be ordered by "score" with the highest value on the top
- The response should include only matched SearchProfiles.

### Search Profile is considered matching when:

- At least one SearchProfile field is matching (strict or loose match)
- No field is miss matching
- Any amount of Search profile fields can be missing.

Example of the valid Search Profile match (for the Property described in the example):

```php
SearchProfile {
"name" => "Looking for any Awesome realestate!",
"propertyType" => "d44d0090-a2b5-47f7-80bb-d6e6f85fca90",
"searchFields" => [
	"price" => ["0","2000000"]
}
```

`price` field is a strict match (1500000 is between 0 and 2000000).

`Area`, `yearOfConstruction` and `rooms` Property fields are missing from the Search Profile

No search Profile fields are miss matching.


### Clone

- Clone the repository using `git clone https://github.com/tosinibrahim96/profile-matcher.git`
- Create a `.env` file in the root folder and copy everything from `.env-sample` into it
- Fill the `.env` values with your Database details as required


### Setup

- Download WAMP or XAMPP to manage APACHE, MYSQL and PhpMyAdmin. This also installs PHP by default. You can follow [this ](https://youtu.be/h6DEDm7C37A)tutorial
- Download and install [composer ](https://getcomposer.org/)globally on your system

> install all project dependencies and generate application key

```shell
$ composer install
$ php artisan key:generate
```
> migrate all tables and seed required data into the database

```shell
$ php artisan migrate:fresh --seed
```
> start your Apache server and MySQL on WAMP or XAMPP interface
> serve your project using the default laravel PORT or manually specify a PORT

```shell
$ php artisan serve (Default PORT)
$ php artisan serve --port={PORT_NUMBER} (setting a PORT manually)
```

### License

- **[MIT license](http://opensource.org/licenses/mit-license.php)**
- Copyright 2021 © <a href="https://tosinibrahim96.github.io/Resume/" target="_blank">Ibrahim Alausa</a>.
