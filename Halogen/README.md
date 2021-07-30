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
DB List
```
OLD: acccomments - Account comments
\ userID | userName | comment | secret | commentID | timestamp | likes | isSpam	

OLD: actions - All actions really
\ ID | type | value | timestamp | value2 | value3 | value4 | value5 | value6 | account

 | !BLOCKS! Replaced by blacklist feature in 'users'
 | Lookup your id in target user blacklist to decide
 
 
OLD: comments - Level comments
\ userID | userName | comment | secret | levelID | commentID | timestamp | likes | percent | isSpam	

OLD: friendreqs - All active friend requests
\ accountID | toAccountID | comment | uploadDate | ID | isNew

OLD: friendships
\ ID | person1 | person2 | isNew2 | isNew1

OLD: levelscores
\ scoreID | accountID | levelID | percent | uploadDate | attempts | coins

OLD: messages
\ userID | userName | body | subject | accID | messageID | toAccountID |timestamp | secret | isNew

OLD: quests - All-time quests (To merge with daily)
\ ID | type | amount | reward | name

OLD: dailyfeatures - daily Table
\ feaID | levelID | timestamp | type

OLD: roles
\ roleID | priority | roleName | modipCategory | isDefault | commentColor | modBadgeLevel
         | commandRate	
         | commandFeature	
         | commandEpic	
         | commandUnepic	
         | commandVerifycoins	
         | commandDaily	
         | commandWeekly	
         | commandDelete	
         | commandSetacc	
         | commandRenameOwn	
         | commandRenameAll	
 JSONify<| commandPassOwn	
 (privs) | commandPassAll	
         | commandDescriptionOwn	
         | commandDescriptionAll	
         | commandPublicOwn	
         | commandPublicAll	
         | commandUnlistOwn	
         | commandUnlistAll	
         | commandSharecpOwn	
         | commandSharecpAll	
         | commandSongOwn	
         | commandSongAll
         | actionRateDemon	
         | actionRateStars	
         | actionSendLevel	
         | actionRateDifficulty	
         | actionRequestMod	
         | toolLeaderboardsban	
         | toolPackcreate	
         | toolModactions	
         | dashboardModTools	

OLD: songs
\ ID | name | authorID | authorName | size | download | hash | isDisabled | levelsCount | reuploadTime	
```
Removed tables:
```
modactions - why not to add them to actions but with MOD mark
roleassign - same
polls - not used anyway
modips - really? who cares
modipperms - same
links - no acc linking, transfer only
bannedips - naah
cpshares - useless anyway
reports - integrated to levels
```

## Reconstructed DB
### Users
```mysql
CREATE TABLE 'users' (
    uid int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    uname varchar(16) NOT NULL,
    passhash varchar(128) NOT NULL,
    email varchar(256) NOT NULL,
    role_id int(4) NOT NULL DEFAULT 0,
    
    stars int(11) NOT NULL DEFAULT 0,
    diamonds int(11) NOT NULL DEFAULT 0,
    coins int(11) NOT NULL DEFAULT 0,
    ucoins int(11) NOT NULL DEFAULT 0,
    demons int(11) NOT NULL DEFAULT 0,
    cpoints int(11) NOT NULL DEFAULT 0,
    orbs int(11) NOT NULL DEFAULT 0,
    
    regDate DATETIME NOT NULL,
    accessDate DATETIME NOT NULL,
    lastIP varchar(64) DEFAULT 'Unknown',
    gameVer int(4) DEFAULT 20,
    lvlsCompleted int(11) DEFAULT 0,
    special int(11) NOT NULL DEFAULT 0,
    
    isBanned tinyint(1) NOT NULL DEFAULT 0,
    blacklist text NOT NULL DEFAULT '',
    friends_cnt int(11) NOT NULL DEFAULT 0,
    friendship_ids TEXT NOT NULL DEFAULT '',
    
    iconType TINYINT NOT NULL DEFAULT 0,
    vessels JSON NOT NULL DEFAULT '{"clr_primary":0,"clr_secondary":0,"cube":0,"ship":0,"ball":0,"ufo":0,"wave":0,"robot":0,"spider":0,"trace":0,"death":0}',
    chests JSON NOT NULL DEFAULT '{"small_count":0,"big_count"":0,"small_time"":0,"big_time":0}',
    settings JSON NOT NULL DEFAULT '{"frS:0","cS:0","mS:0","youtube":"","twitch":"","twitter":""}'
);
```

### Levels
```mysql
CREATE  TABLE 'levels' (
    id int(11) NOT NULL UNIQUE KEY,
    name varchar(32) NOT NULL DEFAULT 'Unnamed',
    description varchar(256) NOT NULL DEFAULT '',
    uid int(11) NOT NULL,
    password varchar(8) NOT NULL,
    version tinyint NOT NULL DEFAULT 1,
    
    length tinyint(1) NOT NULL DEFAULT 0,
    difficulty tinyint(2) NOT NULL DEFAULT 0,
    demonDifficulty tinyint(2) NOT NULL DEFAULT -1,

    track_id mediumint(7) NOT NULL DEFAULT 0,
    song_id mediumint(7) NOT NULL DEFAULT 0,
    versionGame tinyint(3) NOT NULL,
    versionBinary tinyint(3) NOT NULL,
    stringExtra mediumtext NOT NULL,
    stringLevel longtext NOT NULL,
    stringLevelInfo mediumtext NOT NULL,
    original_id int(11) NOT NULL DEFAULT 0,
    
    objects int(11) UNSIGNED NOT NULL,
    starsRequested tinyint(2) NOT NULL,
    starsGot tinyint(2) NOT NULL DEFAULT 0,
    ucoins tinyint(1) NOT NULL,
    coins tinyint(1) NOT NULL DEFAULT 0,
    downloads int(11) UNSIGNED NOT NULL DEFAULT 0,
    likes int(11) UNSIGNED NOT NULL DEFAULT 0,
    reports int(11) UNSIGNED NOT NULL DEFAULT 0,
    
    is2p tinyint(1) NOT NULL DEFAULT 0,
    isVerified tinyint(1) NOT NULL DEFAULT 0,
    isFeatured tinyint(1) NOT NULL DEFAULT 0,
    isHall tinyint(1) NOT NULL DEFAULT 0,
    isEpic tinyint(1) NOT NULL DEFAULT 0,
    isUnlisted tinyint(1) NOT NULL DEFAULT 0,
    isLDM tinyint(1) NOT NULL DEFAULT 0,
    
    uploadDate DATETIME NOT NULL,
    updateDate DATETIME NOT NULL
);
```
Notes:
 - userName [Request from users by uid]
 - difficulty (-1=AUTO 0=N/A 10=EASY 20=NORMAL 30=HARD 40=HARDER 50=INSANE)

### Levelpacks
```mysql
CREATE TABLE 'levelpacks' (
    id int(11) NOT NULL PRIMARY KEY,
    packType tinyint(1) NOT NULL,
    packName varchar(256) NOT NULL,
    levels varchar(512) NOT NULL,
    
    packStars tinyint(3) NOT NULL DEFAULT 0,
    packCoins tinyint(2) NOT NULL DEFAULT 0,
    packDifficulty tinyint(2) NOT NULL,
    packColor varchar(11) NOT NULL
);
```
Notes:
 - packType (0=MapPack 1=Gauntlet)
 - packName (Number if Gauntlet, name if mappack)
 - levels (comma-separated. 5 for gauntlet, 3 for mappack)


