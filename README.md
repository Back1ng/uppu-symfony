File Sharing Uppu (Symfony 5)

How to install:<br>
Clone or download as zip project<br>
Run composer install<br>
Set public directory as public<br>
<br>
Configure database connection & app environment in <code>.env</code> file in core directory.<br>

<code>APP_ENV=dev</code><br>
<code>DATABASE_URL=mysql://user:password@127.0.0.1:3306/uppu</code>

Create migration: <code>php bin/console make:migration</code><br>
Run this migration: <code>php bin/console doctrine:migrations:migrate</code>
