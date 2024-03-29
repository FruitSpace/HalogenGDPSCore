# PLUGINS

## Events:
### Essential
- [X] `preInit` - invoked to load anything
- [X] `unload` - unloads everything
### Player
- [X] `onPlayerNew` - invoked when player is registered, but not yet activated account
- [ ] `onPlayerActivate` - invoked when player first activated account
- [X] `onPlayerLogin` - invoked when player commits login (regular, not gjp)
- [ ] `onPlayerBackup` - invoked when player uploads his backup
- [ ] `onPlayerSync is forbidden`
- [ ] `onPlayerScoreUpdate` - invoked when player updates his score
### Level
- [X] `onLevelUpload` - invoked when level was uploaded
- [X] `onLevelUpdate` - invoked when level was updated
- [X] `onLevelDelete` - invoked when level was deleted
- [X] `onLevelRate` - invoked when level was rated/rerated
- [ ] `onLevelReport` - invoked when level was reported
### LevelPacks
- [ ] `onGauntletNew` - invoked when new gauntlet is created
- [ ] `onMapPackNew` - invoked when new map pack is created
### Communication
- To Be Done


## Descrpition

### Essential
```php
function preInit(PluginCore $pch)

function unload(PluginCore $pch)
```
### Player
```php
function onPlayerNew(PluginCore $pch, int $uid, string $uname, string $email)

function onPlayerActivate(PluginCore $pch, int $uid, string $uname)

function onPlayerLogin(PluginCore $pch, int $uid, string $uname)
```
### Level
```php
function onLevelUpload(PluginCore $pch, int $id, string $name, string $builder, string $desc)

function onLevelUpdate(PluginCore $pch, int $id, string $name, string $builder, string $desc)

function onLevelDelete(PluginCore $pch, int $id, string $name, string $builder)

function onLevelRate(PluginCore $pch, int $id, string $name, string $builder, int $stars, int $likes, int $downloads, int $length, int $demonDiff, bool $isEpic, bool $isFeatured, array[uid,uname] $ratedBy)

function onLevelReport(PluginCore $pch, int $id, string $name, string $builder)
```