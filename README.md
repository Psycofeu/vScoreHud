# vScoreHud by Psycofeu

PMMP Scoreboard 

# TAGS : 
|TAGS|Descritpions|
|:--:|:--:|
| `{player_name}`       | Pseudo of player           |
| `{player_rank}`       | Rank of player             |
| `{faction_name}`      | Faction name of player     |
| `{faction_power}`     | Faction power of player    |
| `{faction_leader}`    | Faction leader             |
| `{money}`             | Player money               |
| `{online_player}`     | Players online             |
| `{max_player}`        | Max player online          |
| `{item_name}`         | Item in hand name          |
| `{item_count}`        | Item in hand count         |
| `{x}`                 | Player position X          |
| `{y}`                 | Player position Y          |
| `{z}`                 | Player position Z          |
| `{world}`             | Player world               |
| `{load}`              | Get ticks usage            |
| `{tps}`               | Get ticks per second       |
| `{ip}`                | Get player IP              |
| `{ping}`              | Get ping                   |
| `{health}`            | Get player health          |
| `{max_health}`        | Get player max health      |
| `{food}`              | Get player food            |
| `{max_food}`          | Get player max food        |
| `{xp_level}`          | Get player XP level        |
| `{xp_progress}`       | Get player XP progress     |
| `{total_xp}`          | Get total player XP        |
| `{world_player_count}`| Get count of player world  |


# plugins import: 
- vanillaEconomy
- EconomyAPI
- BedrockEconomy
- PurePerm
- RankSysteme
- SimpleRank
- PiggyFaction

Config: 

```yaml
#$$$$$$$\                                           $$$$$$\
#$$  __$$\                                         $$  __$$\
#$$ |  $$ | $$$$$$$\ $$\   $$\  $$$$$$$\  $$$$$$\  $$ /  \__|$$$$$$\  $$\   $$\
#$$$$$$$  |$$  _____|$$ |  $$ |$$  _____|$$  __$$\ $$$$\    $$  __$$\ $$ |  $$ |
#$$  ____/ \$$$$$$\  $$ |  $$ |$$ /      $$ /  $$ |$$  _|   $$$$$$$$ |$$ |  $$ |
#$$ |       \____$$\ $$ |  $$ |$$ |      $$ |  $$ |$$ |     $$   ____|$$ |  $$ |
#$$ |      $$$$$$$  |\$$$$$$$ |\$$$$$$$\ \$$$$$$  |$$ |     \$$$$$$$\ \$$$$$$  |
#\__|      \_______/  \____$$ | \_______| \______/ \__|      \_______| \______/
#                    $$\   $$ |
#                    \$$$$$$  |
#                     \______/
# https://discord.gg/vanillamcbe
# https://github.com/psycofeu

refill: 10 # in secondes (10 are recommanded for lags)
enable_disable_world: false
disable_world: ["World", "World2"]
enable_economy: true
economy_plugin: "EconomyAPI" # vanillaEconomy | EconomyAPI | BedrockEconomy

enable_rank: true
rank_plugin: "PurePerm" #PurePerm | RankSysteme | SimpleRank

enable_faction: true
faction_plugin: "piggyfaction" #only piggy faction ;-;


title: "Scoreboard"

# {player_name} = pseudo of player
# {player_rank} = rank of player (with rank plugin)
# {faction_name} = faction name of player
# {faction_power} = faction power of player
# {faction_leader} = faction leader
# {faction_name} = faction name of player
# {faction_name} = faction name of player
# {money} = player money
# {online_player} = players online
# {max_player} = max player online
# {item_name} = item in hand name
# {item_count} = item in hand count
# {x} = player position X
# {y} = player position Y
# {z} = player position Z
# {world} = player world
# {load} = get ticks usage
# {tps} = get ticks per second
# {ip} = get player IP
# {ping} = get ping
# {health} = get player health
# {max_health} = get player max health
# {food} = get player food
# {max_food} = get player max food
# {xp_level} = get player xp level
# {xp_progress} = get player xp progress
# {total_xp} = get total player xp
# {world_player_count} = get count of player world

texte:
  - "§aName: §f{player_name}"
  - "§aRank: §f{player_rank}"
  - "§aFaction: §f{faction}"
  - "§aMoney: §f{money}"
  - ""
  - "§aOnline: §f{online_player}§a/§f{max_player}"
  - "§aPing: §f{ping}"
```

My project:
- [![Discord](https://img.shields.io/discord/1216200805988827267?label=Discord&logo=discord&color=blue)](https://discord.gg/vanillamcbe)


Contactes (for help or suggest to add):
- Discord : psycofeu
- Mail: vifvanilla@gmail.com

bump.
