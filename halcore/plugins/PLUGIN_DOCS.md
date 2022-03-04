# PLUGINS

## Events:
### Essential
- `preInit` - invoked to load anything
- `unload` - unloads everything
### Player
- `onPlayerNew` - invoked when player is registered, but not yet activated account
- `onPlayerActivate` - invoked when player first activated account
- `onPlayerLogin` - invoked when player commits login (regular, not gjp)
- `onPlayerBackup` - invoked when player uploads his backup
- `onPlayerSync is forbidden`
- `onPlayerScoreUpdate` - invoked when player updates his score
### Level
- `onLevelUpload` - invoked when level was uploaded
- `onLevelUpdate` - invoked when level was updated
- `onLevelDelete` - invoked when level was deleted
- `onLevelRate` - invoked when level was rated/rerated
- `onLevelReport` - invoked when level was reported
### LevelPacks
- `onGauntletNew` - invoked when new gauntlet is created
- `onMapPackNew` - invoked when new map pack is created
### Communication
- To Be Done


## Descrpition

### Essential
```php
function preInit(PluginCore $pch)

function unload(PluginCore $pch)
```
### Player

### Level
```php
function onLevelUpload(PluginCore $pch, int $id, string $name, string $builder, string $desc)

function onLevelUpdate(PluginCore $pch, int $id, string $name, string $builder, string $desc)

function onLevelDelete(PluginCore $pch, int $id, string $name, string $builder)

function onLevelRate(PluginCore $pch, int $id, string $name, string $builder, int $stars, int $likes, int $downloads, int $length, bool $isEpic, bool $isFeatured, array[uid,uname] $ratedBy)

function onLevelReport(PluginCore $pch, int $id, string $name, string $builder)
```