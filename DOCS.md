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

account: done
- [X] account/accountBackup
- [X] account/accountLogin
- [X] account/accountManagement (http://s.halhost.cc/userpanel/)
- [X] account/accountRegister
- [X] account/accountSync
---

comment: done
- [X] comment/accountComment_delete
- [X] comment/accountComment_get
- [X] comment/accountComment_upload
- [X] comment/comment_delete
- [X] comment/comment_get
- [X] comment/comment_upload
---

communication: done
- [X] communication/blockUser
- [X] communication/friend_acceptRequest
- [X] communication/friend_deleteRequest
- [X] communication/friend_getRequests
- [X] communication/friend_readRequest
- [X] communication/friend_remove
- [X] communication/friend_request
- [X] communication/message_delete
- [X] communication/message_get
- [X] communication/message_getAll
- [X] communication/message_upload
- [X] communication/unblockUser
---

essential: done
- [X] essential/getAccountUrl
- [X] essential/getSongInfo
- [X] essential/getTopArtists
- [X] essential/likeItem
- [X] essential/requestMod
---

level:
- [ ] level/getGauntlets
- [ ] level/getMapPacks
- [X] level/level_delete
- [X] level/level_download
- [ ] level/level_getDaily
- [X] level/level_getLevels
- [ ] level/level_report
- [X] level/level_updateDescription
- [X] level/level_upload
- [ ] level/rateDemon
- [X] level/rateStar
- [X] level/suggestStar
---

profile: done
- [X] profile/getUserInfo
- [X] profile/getUserList
- [X] profile/getUsers
- [X] profile/updateAccountSettings
---

rewards:
- [ ] rewards/getChallenges
- [X] rewards/getRewards
---

score: done
- [X] score/getCreators
- [X] score/getLevelScores
- [X] score/getScores
- [X] score/updateUserScore
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
    id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name varchar(32) NOT NULL DEFAULT 'Unnamed',
    description varchar(256) NOT NULL DEFAULT '',
    uid int(11) NOT NULL,
    password varchar(8) NOT NULL,
    version tinyint NOT NULL DEFAULT 1,
    
    length tinyint(1) NOT NULL DEFAULT 0,
    difficulty tinyint(2) NOT NULL DEFAULT 0,
    demonDifficulty tinyint(2) NOT NULL DEFAULT -1,
    suggestDifficulty float(2,1) NOT NULL DEFAULT 0,
    suggestDifficultyCnt int(11) NOT NULL DEFAULT 0,

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
    likes int(11) NOT NULL DEFAULT 0,
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
 - lvl_id (only for daily/weekly)
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
   deleteQuest/editQuest), 6->likeLevelEvent(like/dislike), 7->likeAccCommentEvent(like/dislike),
   8->likeCommentEvent(like/dislike)
 - events 6,7,8 are used to avoid subbot
 - data (wtf will the panel support who caress)


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
