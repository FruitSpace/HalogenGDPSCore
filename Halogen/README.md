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

OLD: dailyfeatures - daily Table
\ feaID | levelID | timestamp | type	

OLD: friendreqs - All active friend requests
\ accountID | toAccountID | comment | uploadDate | ID | isNew

OLD: friendships
\ ID | person1 | person2 | isNew2 | isNew1

OLD: gauntlets
\ ID | level1 | level2 | level3 | level4 | level5	

OLD: mappacks
\ ID | name | levels | stars | coins | difficulty | rgbcolors | colors2

OLD: levels
\ gameVersion | binaryVersion | userName | levelID | levelName | levelDesc | levelVersion	
 | levelLength | audioTrack | auto | password | original | twoPlayer | songID | objects	
 | coins | requestedStars | extraString | levelString | levelInfo | secret | starDifficulty
 | downloads | likes | starDemon | starAuto | starStars | uploadDate | updateDate | rateDate	
 | starCoins | starFeatured | starHall | starEpic | starDemonDiff | userID | extID | unlisted	
 | originalReup | hostname | isCPShared | isDeleted | isLDM	

OLD: levelscores
\ scoreID | accountID | levelID | percent | uploadDate | attempts | coins

OLD: messages
\ userID | userName | body | subject | accID | messageID | toAccountID |timestamp | secret | isNew

OLD: quests - All-time quests (To merge with daily)
\ ID | type | amount | reward | name

OLD: reports - Report levels (Anon)
\ ID | levelID | hostname

OLD: roleassign - Role assign actions
\ assignID | roleID | accountID

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
polls - not used anyway
modips - really? who cares
modipperms - same
links - no acc linking, transfer only
bannedips - naah
cpshares - useless anyway
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
    secret varchar(16),
    special int(11) NOT NULL DEFAULT 0,
    
    banned tinyint(1) NOT NULL DEFAULT 0,
    blacklist text NOT NULL DEFAULT '',
    friends_cnt int(11) NOT NULL DEFAULT 0,
    friendship_ids TEXT NOT NULL DEFAULT '',
    
    iconType TINYINT NOT NULL DEFAULT 0,
    vessels JSON NOT NULL DEFAULT '{"clr_primary":0,"clr_secondary":0,"cube":0,"ship":0,"ball":0,"ufo":0,"wave":0,"robot":0,"spider":0,"trace":0,"death":0}',
    chests JSON NOT NULL DEFAULT '{"small_count":0,"big_count"":0,"small_time"":0,"big_time":0}',
    settings JSON NOT NULL DEFAULT '{"frS:0","cS:0","mS:0","yt_url":"","twitch":"","twitter":""}'
);
```

### Levels
```mysql

levelID
levelName
levelDescription
levelVersion	
levelLength

gameVersion
binaryVersion
userName
userID
audioID  \___ Merge like <25 then internal audio else song ID
songID   /
password
originalId (0 if no orig)

isTwoPlayer
objects	
coins
stars_requested
stars_got
extraString
levelString
levelInfo
secret
difficuty (0=N/A 10=EASY 20=NORMAL 30=HARD 40=HARDER 50=INSANE)
downloads
likes
isDemon
isAuto
uploadDate
updateDate
rateDate	
isVerified
isFeatured
starHallOfFame
isEpic
demonDifficulty
isUnlisted
isLowDetail (LDM)
```