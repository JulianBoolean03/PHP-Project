# How to Disable Auto-Refresh

If the auto-refresh is too annoying, you can disable it and use manual refresh only (pressing F5).

## Current Settings

- **Lobby**: Auto-refreshes every 5 seconds
- **Game Board**: Auto-refreshes every 8 seconds
- **Question Page**: Auto-refreshes when timer expires

## To Disable Auto-Refresh

### Option 1: Comment Out Meta Refresh Tags

Edit these files and comment out or remove the meta refresh lines:

#### `lobby.php` (line ~85):
```php
<!-- Auto-refresh every 5 seconds to check for game start and new players -->
<!-- <meta http-equiv="refresh" content="5"> -->
```

#### `game.php` (line ~103):
```php
<!-- Auto-refresh every 8 seconds to see updates from other players -->
<!-- <meta http-equiv="refresh" content="8"> -->
```

### Option 2: Increase Refresh Intervals

Make them much longer (e.g., 30-60 seconds) so they're less noticeable:

#### `lobby.php`:
```html
<meta http-equiv="refresh" content="30">
```

#### `game.php`:
```html
<meta http-equiv="refresh" content="30">
```

## Manual Refresh Instructions

If you disable auto-refresh, tell players:

### In Lobby:
- **Press F5** to see new players joining
- **Press F5** to check if game has started
- After someone clicks "Start Game", other players must press F5 within a reasonable time

### In Game:
- **Press F5** after selecting a question to see the question page
- **Press F5** after another player answers to see updated board and scores
- **Press F5** to see whose turn it is

## Hybrid Approach (Recommended)

Keep auto-refresh but increase intervals to reduce annoyance:

- **Lobby**: 10 seconds (gives time to read, but still auto-detects game start)
- **Game Board**: 15 seconds (less disruptive, players can manually refresh if impatient)

Edit the files:

#### `lobby.php`:
```html
<meta http-equiv="refresh" content="10">
```

#### `game.php`:
```html
<meta http-equiv="refresh" content="15">
```

## Trade-offs

### With Auto-Refresh:
- ✅ Players automatically see updates
- ✅ Both players automatically enter game when started
- ❌ Screen flashes/blinks when refreshing
- ❌ Can be disorienting

### Without Auto-Refresh (Manual Only):
- ✅ No screen flashing
- ✅ More control over when to check updates
- ❌ Players must remember to press F5
- ❌ One player may not know game has started
- ❌ Less "real-time" feel

### With Longer Intervals (10-15 seconds):
- ✅ Balance between automatic and smooth
- ✅ Still catches updates reasonably fast
- ✅ Less disruptive than 2-3 seconds
- ⚠️ Slightly slower multiplayer experience

## Current Implementation

I've already changed the intervals to:
- Lobby: **5 seconds** (was 2)
- Game: **8 seconds** (was 3)

This should be less annoying while still providing a decent multiplayer experience.
