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

## Ignore this
DB Merge: **users** + **accounts**
```
-----auth info
userID
extID
accountID=extID
isAdmin
userName
password
email
-----primary account stuff
+ stars
+ diamonds
+ coins
+ userCoins
+ demons
+ creatorPoints (cp)
+ orbs
icon (actual iconID like acc****)
iconType (id like 4=glider,0=cube etc)

-----tech info
gameVersion
IP
lastPlayed
completedLvls
special
secret
registerDate

-----friendship stuff
isBanned
friends
friendsCount
blockedBy
blocked

-----JSONed

                      / color_primary
                     / color_secondary
                    / accIcon
                   / accShip
                  / accBall
                 / accBird (ufo)
vessels (json) <| accDart (Glider)
                 \ accRobot
                  \ accGlow ? Can we fix that
                   \ accSpider
                    \ accExplosion

                     / chest_small_time
                    / chest_big_time
chest_data (json) <| chest_small_count
                    \ chest_big_count

                         / frS (Allow friend requests from)
                        / cS (Show comment history to)
                       / youtubeurl
acc_settings (json)  <| twitter
                       \ twitch
                        \ mS (Allow messages from)
```

Removed:
```
isRegistered - ofc obv!
isCreatorBanned - who cares
```