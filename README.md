# Wesley's Atarim Tech Task

## Running

To run the project, you can use the following commands from the root of the project:

N.B. You will need to have Docker installed and running on your machine to run the project.

```
composer install
vendor/bin/sail up
vendor/bin/sail artisan migrate
```

## Testing

To run the tests, you can use the following command from the root of the project:

```
vendor/bin/sail test
```

I didn't add unit tests for this task, as I felt that the feature tests covered the main functionality of the service.

## Approach

I chose to create a simple service backed by an interface to handle the encoding and decoding of URLs.
I then used a factory pattern to create the service and return the correct implementation based on the configuration.
This allows for easy swapping of the implementation in the future if needed, therefore making the code more maintainable and future-proof.

I made all the key properties of the service configurable via the .env file, so that they can be easily changed without needing to modify the code.
But have set sane defaults in the base configuration file, so no changes are needed to run the project.

I used a database, as this is how these services generally work, and there are no decent alternatives that I could think of that would be as performant, scalable or produce the shortening required.
Using a database comes with some benefits such as:

- The ability to store the original URL and the shortened URL in the same record, making it easier to retrieve the original URL from the shortened URL.
- Off-loading all the work to the encoding method, thereby making writes slower but reads faster, which is generally where the bottleneck is.
- Allowing analytics to be easily added in the future, such as the number of times a URL has been accessed, the time it was last accessed, set an expiry date on a URL, or to limit the number of times a URL can be accessed etc.

There were other approaches I could have taken instead of going with my hash implementation, such as using hashids using a library such as:

- [https://sqids.org](https://sqids.org)
- [https://github.com/vinkla/laravel-hashids](https://github.com/vinkla/laravel-hashids)
- [https://github.com/deligoez/laravel-model-hashid](https://github.com/deligoez/laravel-model-hashid)

Although, a driver can easily be added to the service factory to use one of these libraries if needed in the future.

A slight downside to the above (if the hashid isn't stored), would be the extra processing required to decode a hashid into the primary key of the record, then the primary key used to retrieve the record from the database.

N.B. I didn't optimise or remove default junk from Laravel, in a real world scenario I would have done this.

## File locations

`app/Http/Controllers/UrlController.php` - The controller for the service.

`app/Http/Resources/UrlResource.php` - The JSON resource for the controller.

`app/Models/Url.php` - The model for the service.

`app/Services/UrlShortener/UrlShortener.php` - The factory for the service.

`app/Services/UrlShortener/UrlShortenerInterface.php` - The interface for the service.

`app/Services/UrlShortener/Hash/HashShortener.php` - A hash implementation of the service.

`config/shortener.php` - The configuration file for the service.

`database/factories/UrlFactory.php` - The factory for the model.

`database/migrations/2024_04_15_174415_create_urls_table.php` - The migration for the model.

`tests/Feature/UrlShortenerTest.php` - The feature tests for the service.
