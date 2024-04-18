To use the Chamilo HOGENT API you must first create a public and private certificate in the configuration folder

```
openssl genrsa -out private.key 2048
openssl rsa -in private.key -pubout -out public.key
```
