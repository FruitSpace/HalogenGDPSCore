# Halogen GDPS Core
## Geometry Dash Private Server
**File Tree:**
```
📁 [ROOT]
|__ 📁 database | GD Redirect Endpoints
|__ 📁 api | GD Actual Endpoints
|__ 📁 conf | Configuration files
|__ 📁 halcore | Core itsef
```

## Complains
**Cvolton GDPS** uses `defuse-crypto`. Do you know what it is?
```php
const CIPHER_METHOD = 'aes-256-ctr';
...
const HASH_FUNCTION_NAME = 'sha256';
```
This is a hecking wrapper around built-in **openssl** lib. What a shame! (So I removed that lib)