To use the Chamilo HOGENT API you must first create a public and private certificate in the configuration folder (configuration/api)

```
openssl genrsa -out private.key 2048
openssl rsa -in private.key -pubout -out public.key
```

You must then create a file called secret.php in the configuration folder (configuration/api) with the following configuration

```
<?php

$apiSecret = 'YOUR_SECRET';

```
