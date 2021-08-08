## Ignore this

###Important
**Security GJP Check:**
 - Sessions->get ip&lastAccessDate (1Hr Limit) or request password

**Logging**
 - `DBM` Verbose, Fatal (die)
 - `HAL_LIMIT` Error
 - `ENDPOINT` Verbose
 - `ThunderAES` Error (die)
 - `CFriendship` Error (die)

### Endpoints
- [X] account/accountBackup
- [X] account/accountLogin
- [X] account/accountManagement (http://s.halhost.cc/userpanel/)
- [X] account/accountRegister
- [X] account/accountSync
---

- [X] comment/accountComment_delete
- [X] comment/accountComment_get
- [X] comment/accountComment_upload
- [ ] comment/comment_delete
- [ ] comment/comment_get
- [ ] comment/comment_upload
---

- [X] communication/blockUser
- [X] communication/friend_acceptRequest
- [X] communication/friend_deleteRequest
- [X] communication/friend_getRequests
- [X] communication/friend_readRequest
- [X] communication/friend_remove
- [X] communication/friend_request
- [ ] communication/message_delete
- [ ] communication/message_get
- [ ] communication/message_upload
- [X] communication/unblockUser
---

- [X] essential/getAccountUrl
- [ ] essential/getSongInfo
- [ ] essential/getTopArtists
- [ ] essential/likeItem
---

- [ ] level/getGauntlets
- [ ] level/getMapPacks
- [X] level/level_delete
- [ ] level/level_download
- [ ] level/level_getDaily
- [ ] level/level_getLevels
- [ ] level/level_report
- [ ] level/level_updateDescription
- [ ] level/level_upload
- [ ] level/rateDemon
- [ ] level/rateStar
- [ ] level/suggestStar
---

- [ ] profile/getUserInfo
- [ ] profile/getUserList
- [ ] profile/getUsers
- [ ] profile/updateAccountSettings
---

- [ ] rewards/getChallenges
- [ ] rewards/getRewards
---

- [ ] score/getCreators
- [ ] score/getLevelScores
- [ ] score/getScores
- [ ] score/updateUserScore
---

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
CREATE TABLE users (
    uid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
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
    chests JSON NOT NULL DEFAULT '{"small_count":0,"big_count":0,"small_time":0,"big_time":0}',
    settings JSON NOT NULL DEFAULT '{"frS":0,"cS":0,"mS":0,"youtube":"","twitch":"","twitter":""}'
);
```

### Levels
```mysql
CREATE  TABLE levels (
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
CREATE TABLE levelpacks (
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
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

### Roles
```mysql
CREATE TABLE roles (
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    roleName varchar(64) NOT NULL DEFAULT 'Moderator',
    commentColor varchar(11) NOT NULL DEFAULT '0,0,255',
    modLevel tinyint(1) NOT NULL DEFAULT 1,
    privs text  NOT NULL DEFAULT '{"cRate":0,"cFeature":0,"cEpic":0,"cUnepic":0,"cVerCoins":0,"cDaily":0,"cWeekly":0,"cDelete":0,"cSetacc":0,"aRateDemon":0,"aRateStars":0,"aReqMod":0,"dashboardMod":0,"dashboardCreatePack":0}'
);
```

### Songs
```mysql
CREATE TABLE songs (
    id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    author_id int(11) NOT NULL DEFAULT 0,
    name varchar(128) NOT NULL DEFAULT 'Unnamed',
    author_name varchar(128) NOT NULL DEFAULT 'Unknown',
    size varchar(5) NOT NULL,
    url varchar(1024) NOT NULL,
    isBanned tinyint(1) NOT NULL DEFAULT 0
);
```

### Friendships
```mysql
CREATE TABLE friendships (
    id int(12) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    uid1 int(11) NOT NULL,
    uid2 int(11) NOT NULL,
    u1_new tinyint(1) NOT NULL DEFAULT 1,
    u2_new tinyint(1) NOT NULL DEFAULT 1
);
```

### FriendReqs
```mysql
CREATE TABLE friendreqs (
    id int(12) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    uid_src int(11) NOT NULL,
    uid_dest int(11) NOT NULL,
    uploadDate DATETIME NOT NULL,
    comment varchar(512) NOT NULL DEFAULT '',
    isNew tinyint(1) NOT NULL DEFAULT 1
);
```

### AccountComments
```mysql
CREATE TABLE acccomments (
    id int(12) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    uid int(11) NOT NULL,
    comment varchar(128) NOT NULL,
    postedTime DATETIME NOT NULL,
    likes int(11) NOT NULL DEFAULT 0,
    isSpam tinyint(1) NOT NULL DEFAULT 0
);
```

### Comments
```mysql
CREATE TABLE comments (
    id int(12) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    uid int(11) NOT NULL,
    lvl_id int(11) NOT NULL,
    comment varchar(128) NOT NULL,
    postedTime DATETIME NOT NULL,
    likes int(11) NOT NULL DEFAULT 0,
    isSpam tinyint(1) NOT NULL DEFAULT 0,
    percent tinyint(3) NOT NULL
);
```

### Scores
```mysql
CREATE TABLE scores (
    id int(12) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    uid int(11) NOT NULL,
    lvl_id int(11) NOT NULL,
    postedTime DATETIME NOT NULL,
    percent tinyint(3) NOT NULL,
    attempts int(11) NOT NULL DEFAULT 0,
    coins tinyint(1) NOT NULL DEFAULT 0
);
```

### Messages
```mysql
CREATE TABLE messages (
    id int(12) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    uid_src int(11) NOT NULL,
    uid_dest int(11) NOT NULL,
    subject varchar(256) NOT NULL DEFAULT '',
    body varchar(1024) NOT NULL,
    postedTime DATETIME NOT NULL,
    isNew tinyint(1) NOT NULL DEFAULT 1
);
```

### Quests
```mysql
CREATE TABLE quests (
    id int(12) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    type tinyint(1) NOT NULL,
    name varchar(64) NOT NULL DEFAULT '',
    needed int(7) NOT NULL DEFAULT 0,
    reward int(7) NOT NULL DEFAULT 0,
    lvl_id int(11) NOT NULL DEFAULT 0,
    timeExpire DATETIME NOT NULL
);
```
Notes:
 - type (0 - dailylevel, 1 - weeklylevel, 2 - orbs, 3 - coins, 4 - stars)
 - needed (only for quests)
 - reward (only for quests)
 - name (only for quests)
 - lvl_id (only for daily)
 - timeExpire (when reload quests/update level)

### Actions
```mysql
CREATE TABLE actions (
    id int(13) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    date DATETIME NOT NULL,
    uid int(11) NOT NULL,
    type tinyint(1) NOT NULL,
    target_id int(11) NOT NULL,
    isMod tinyint(1) NOT NULL DEFAULT 0,
    data JSON NOT NULL DEFAULT '{}'
);
```
Notes:
 - uid=0 if server made action
 - type (0->register, 1->login, 2->delete, 3->banEvent(Ban/Unban), 4->levelEvent(Upload/Delete/Update),
   5->panelEvents(addGauntlet/deleteGauntlet/editGauntlet/addMapPack/deleteMapPack/editMapPack/addQuest/
   deleteQuest/editQuest))
 - data (wtf will the panel support who caress)

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





1,2,3,4,8,  10,11,13,16,17,18,19,20,21,22,23,24,25,26,28,29,30,31,32,35,37,43,44,45,46,47,49,50



## God Tier Quotes
```php
if($weekly == 1){
	$dailyID = $dailyID + 100001; //the fuck went through robtops head when he was implementing this
}

...

	//RESPONSE SO IT DOESNT SAY "FAILED"
	echo "1";
}else{
	//OR YOU KNOW WHAT LETS MAKE IT SAY "FAILED"
	echo "-1";
}
```