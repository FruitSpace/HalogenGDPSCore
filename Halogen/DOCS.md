## Ignore this
Endpoints:
- [X] account/accountBackup
- [X] account/accountLogin
- [X] account/accountManagement (http://s.halhost.cc/userpanel/)
- [X] account/accountRegister
- [X] account/accountSync


- [ ] comment/accountComment_delete
- [ ] comment/accountComment_get
- [ ] comment/accountComment_upload
- [ ] comment/comment_delete
- [ ] comment/comment_get
- [ ] comment/comment_upload


- [ ] communication/blockUser
- [ ] communication/friend_acceptRequest
- [ ] communication/friend_deleteRequest
- [ ] communication/friend_getRequest
- [ ] communication/friend_readRequest
- [ ] communication/friend_remove
- [ ] communication/friend_request
- [ ] communication/message_delete
- [ ] communication/message_get
- [ ] communication/message_upload
- [ ] communication/unblockUser


- [ ] essential/getAccountUrl
- [ ] essential/getSongInfo
- [ ] essential/getTopArtists
- [ ] essential/likeItem


- [ ] level/getGauntlets
- [ ] level/getMapPacks
- [ ] level/level_deleteUser
- [ ] level/level_download
- [ ] level/level_getDaily
- [ ] level/level_getLevels
- [ ] level/level_report
- [ ] level/level_updateDescription
- [ ] level/level_upload
- [ ] level/rateDemon
- [ ] level/rateStar
- [ ] level/suggestStar


- [ ] profile/getUSerInfo
- [ ] profile/getUserList
- [ ] profile/getUsers
- [ ] profile/updateAccountSettings


- [ ] rewards/getChallenges
- [ ] rewards/getRewards


- [ ] score/getCreators
- [ ] score/getLevelScores
- [ ] score/getScores
- [ ] score/updateUserScore

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


##UserDat Format
| Key | Name/Value | Description |
| --- | ---------- | ----------- |
| 1 | uname | The name of player |
| 2 | uid | The ID of player |
| 3 | stars | The count of stars player have |
| 4 | demons | The count of demons player have |
| 6 | ranking | the global leaderboard position of the player |
| 7 | accountHighlight | The accountID of the player. Is used for highlighting the player on the leaderboards |
| 8 | creatorpoints | The count of creatorpoints player have |
| 9 | iconID | maybe... [link](https://github.com/gd-programming/gddocs/pull/16/files#r417926661) |
| 10 | playerColor | First color of the player use |
| 11 | playerColor2 | Second color of the player use |
| 13 | secretCoins | The count of coins player have |
| 14 | iconType | The iconType of the player use |
| 15 | special | The special number of the player use |
| 16 | accountID | The accountid of this player |
| 17 | usercoins | The count of usercoins player have |
| 18 | messageState | 0: All, 1: Only friends, 2: None |
| 19 | friendsState | 0: All, 1: None |
| 20 | youTube |  The youtubeurl of player |
| 21 | accIcon | The icon number of the player use |
| 22 | accShip | The ship number of the player use |
| 23 | accBall | The ball number of the player use |
| 24 | accBird | The bird number of the player use |
| 25 | accDart(wave) | The dart(wave) number of the player use |
| 26 | accRobot | The robot number of the player use |
| 27 | accStreak | The streak of the user |
| 28 | accGlow | The glow number of the player use |
| 29 | isRegistered | if an account is registered or not |
| 30 | globalRank | The global rank of this player |
| 31 | friendstate | 0: None, 1: already is friend, 3: send request to target, but target haven't accept, 4: target send request, but haven't accept
| 38 | messages | How many new messages the user has (shown in-game as a notification) | <!-- there are a bunch of keys before here but they are exclusive to friend requests so i didnt add them here -->
| 39 | friendRequests | How many new friend requests the user has (shown in-game as a notificaiton) |
| 40 | newFriends | How many new Friends the user has (shown in-game as a notificaiton) |
| 41 | hasBlocked |  appears on userlist endpoint to show if the user is blocked |
| 42 | age |  the time since you submitted a levelScore |
| 43 | accSpider | The spider number of the player use |
| 44 | twitter|  The twitter of player |
| 45 | twitch |  The twitch of player |
| 46 | diamonds | The count of diamonds player have |
| 48 | accExplosion | The explosion number of the player use |
| 49 | modlevel | 0: None, 1: Normal Mod(yellow), 2: Elder Mod(orange) |
| 50 | commentHistoryState | 0: All, 1: Only friends, 2: None |





1,2,3,4,8.10,11,13,16,17,18,19,20,21,22,23,24,25,26,28,29,30,31,32,35,37,43,44,45,46,47,49,50