# pulunomoe/puluauth

Super simple, [client credentials grant](https://datatracker.ietf.org/doc/html/rfc6749#section-4.4) only OAuth2 server. Build on top of [PHPLeague's OAuth2 server](https://oauth2.thephpleague.com/).

## Requirements

- PHP 8+
- PDO extension

## Usage

1. Clone this repository.
2. Install the dependencies:<br>`composer install && composer dump-autoload`.
3. Run the database scripts inside the `db` directory.
4. Generate a private key:<br>`openssl genrsa -aes128 -passout pass:YOUR_PASSWORD_HERE_ -out private.key 2048`
5. Generate a public key:<br>`openssl rsa -in private.key -passin pass:YOUR_PASSWORD_HERE -pubout -out public.key`
6. Generate an encryption key:<br>`vendor/bin/generate-defuse-key > defuse.key`
7. Put the keys inside the keys directory (make sure it's not publicly accessible!)
8. Change the access level of the keys to `600`, i.e:<br>`chmod 600 *.keys`
9. Run the app with your web server, with the `public` directory as the root directory.
10. Open the admin panel (`/admin/login`), use the default admin username and password configuration: `admin` / `admin`
11. Change your admin password.
12. Optionally, modify your network so the `/admin` path only accessible from trusted hosts.

## Changelog

- v0.1 : Initial version
