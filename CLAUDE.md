# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a **Pokemon Database Management System** built with Laravel 12, Filament 4, and Livewire. The application imports Pokemon data from the PokeAPI and provides a comprehensive admin panel for browsing Pokemon, moves, abilities, types, items, and evolution chains.

**Tech Stack:**
- Laravel 12 (PHP 8.4)
- Filament 4 (admin panel framework)
- Livewire & Flux (reactive components)
- SQLite database (default)
- PokeAPI integration for data seeding

## Common Commands

### Development

```bash
# Start development environment (server + queue + logs + vite in parallel)
composer dev

# Or manually run Laravel development server
php artisan serve

# Run tests
composer test
# Or directly
php artisan test

# Run specific test file
php artisan test tests/Feature/ExampleTest.php

# Code formatting (Laravel Pint)
./vendor/bin/pint

# View logs in real-time
php artisan pail --timeout=0

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Database & Seeding

```bash
# Run migrations
php artisan migrate

# Seed all Pokemon data from PokeAPI (in order)
php artisan seed:all

# Seed specific data types
php artisan seed:types
php artisan seed:abilities
php artisan seed:moves
php artisan seed:items
php artisan seed:pokemon-species
php artisan seed:evolution-chains
php artisan seed:pokemon

# Seed with options
php artisan seed:all --threads=4 --delay=100 --limit=50

# Fresh migration + seed
php artisan migrate:fresh --seed
```

**Important:** Seeding order matters due to foreign key dependencies:
1. Types → 2. Abilities → 3. Moves → 4. Items → 5. Pokemon Species → 6. Evolution Chains → 7. Pokemon

### Docker (Production)

```bash
# Deploy with Docker Compose (production)
docker compose -f docker-compose.prod.yml up -d

# View logs
docker compose -f docker-compose.prod.yml logs -f

# Run artisan commands in container
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Access container shell
docker compose -f docker-compose.prod.yml exec app sh
```

See `DOCKER.md` for full deployment guide.

### Frontend

```bash
# Build assets
npm run build

# Watch for changes (development)
npm run dev
```

## Architecture Overview

### Database Models & Relationships

**Core Models:**
- `Pokemon`: Individual Pokemon forms with stats, sprites, cries
  - Belongs to: `PokemonSpecies`
  - Many-to-many: `Type`, `Ability`, `Move`, `Item`
  - Has many: `PokemonStat`, `PokemonGameIndex`
  - Route key: `api_id` (not database `id`)

- `PokemonSpecies`: Species-level data (shared across forms)
  - Fields: `name`, `base_happiness`, `capture_rate`, `color`, `gender_rate`, `is_legendary`, `is_mythical`, `habitat`, `generation`
  - Belongs to: `EvolutionChain`
  - Has many: `Pokemon`, `Evolution`

- `Move`: Pokemon moves/attacks
  - Fields: `name`, `power`, `pp`, `accuracy`, `priority`, `damage_class`, `effect`
  - Belongs to: `Type`
  - Many-to-many with `Pokemon` (pivot: `learn_method`, `level_learned_at`)

- `Evolution`: Evolution relationships with complex conditions
  - Belongs to: `EvolutionChain`, `PokemonSpecies` (from/to)
  - Fields: `trigger`, `min_level`, `item`, `held_item`, `gender`, `min_happiness`, `location`, `time_of_day`, etc.

- `EvolutionChain`: Groups evolution stages
  - Has many: `PokemonSpecies`, `Evolution`

**Pivot Tables:**
- `pokemon_type`: Types with `slot` (primary/secondary)
- `ability_pokemon`: Abilities with `is_hidden` and `slot`
- `move_pokemon`: Moves with `learn_method` and `level_learned_at`
- `pokemon_item`: Items with `rarity` and `version`

### Filament Resource Structure

Filament resources follow a **modular schema pattern**:

```
app/Filament/Resources/{ResourceName}/
├── {ResourceName}Resource.php          # Main resource configuration
├── Pages/                               # CRUD pages (List, Create, Edit, View)
├── Schemas/                            # Reusable UI component definitions
│   ├── {ResourceName}Form.php          # Form schema
│   ├── {ResourceName}Infolist.php      # Detail view schema
│   └── Components/                     # Reusable section components
└── Tables/                             # Table configuration
    └── {ResourceName}Table.php
```

**Key Resources:**
- `PokemonResource`: Main Pokemon CRUD with stats, sprites, evolutions, and moves
- `MoveResource`: Move details with learned-by Pokemon table
- `AbilitiesResource`, `ItemsResource`, `TypesResource`, `UsersResource`

### Custom Patterns

**1. Reusable Filters** (`app/Filament/Filters/`)
- `TypesFilter::make(columns: 3)`: Multi-mode filtering (any/all/none/only)
- `LearnMethodFilter::make(columns: 3, pokemon: $pokemon)`: Context-aware filtering
- `MoveTypeFilter`, `MoveClassFilter`: Move-specific filters

**2. Embedded Livewire Tables** (`app/Livewire/`)
- `PokemonMovesTable`: Shows all moves a Pokemon can learn (filterable)
- `MovePokemonTable`: Shows all Pokemon that learn a move
- Embedded in Filament infolists via `Livewire::make(PokemonMovesTable::class, ...)`

**3. Schema Components** (`app/Filament/Resources/*/Schemas/Components/`)
- Reusable UI sections as static classes
- Example: `EvolutionsSection` dynamically renders 1-3 evolution stages with clickable sprites
- Uses model methods like `getEvolutionChainData()` for complex data transformation

**4. Global Model Scopes**
- All main models order by `api_id` by default:
  ```php
  static::addGlobalScope('ordered', fn($query) => $query->orderBy('api_id'));
  ```

**5. Attribute Accessors**
- Name formatting: `ucwords(str_replace('-', ' ', $value))`
- Unit conversion: weight/height from API units to display units
- Stat retrieval: Virtual attributes like `hp_stat`, `total_base_stat`
- Evolution chain flattening: `getEvolutionChainData()` returns `stage_1_sprite`, `stage_1_name`, etc.

### Data Seeding Architecture

**PokeAPI Integration:**
- `app/Services/PokeApiService.php`: HTTP wrapper for PokeAPI
- `database/seeders/BasePokeApiSeeder.php`: Abstract base with pagination, batching, rate limiting
- Individual seeders: `TypeSeeder`, `AbilitySeeder`, `MoveSeeder`, `PokemonSeeder`, etc.
- Console commands: `app/Console/Commands/Seed/` with `SeedAll` orchestrator

**Seeding Features:**
- Progress bars for large datasets
- Batch processing with error handling
- Rate limiting to prevent API throttling
- Parallel processing support (`--threads` option)
- Customizable limits and delays

## Important Conventions

1. **Use `api_id` for routing**: Models use `api_id` instead of database `id` for URLs
   - URLs like `/pokemon/25` (Pikachu) instead of `/pokemon/123`

2. **Maintain seeding order**: Foreign key dependencies require specific order (see above)

3. **Static `make()` methods**: Reusable filters and components use static factory methods

4. **Separate UI logic**: Keep form/infolist/table schemas in separate files under `Schemas/`

5. **Leverage global scopes**: Models are consistently ordered by `api_id`

6. **Pivot tables with metadata**: Many-to-many relationships include additional data (slots, learn methods, etc.)

7. **Format strings consistently**: `ucwords(str_replace('-', ' ', $value))`

8. **Embed Livewire in Filament**: Use Livewire components for complex relationship tables within infolists

9. **Color-code by type**: Use type name as Filament color identifier for consistent theming

10. **Handle soft deletes**: Resources must handle `withoutGlobalScopes([SoftDeletingScope::class])`

## Model-Specific Notes

### Pokemon Model

```php
// Stat accessors (retrieve from related PokemonStat)
$pokemon->hp_stat
$pokemon->attack_stat
$pokemon->total_base_stat

// Evolution chain data (flattened for UI)
$pokemon->getEvolutionChainData()
// Returns: ['stage_1_sprite' => '...', 'stage_1_name' => 'Bulbasaur', 'stage_1_method' => 'Level 16', ...]

// Unit conversions
$pokemon->weight // Converted from hectograms to kg
$pokemon->height // Converted from decimeters to m
```

### Stat Sorting in Tables

Pokemon stats are in a separate table, requiring custom query logic:

```php
->sortable(query: function ($query, $direction) {
    return $query
        ->leftJoin('pokemon_stats as hp_stats', function ($join) {
            $join->on('pokemon.id', '=', 'hp_stats.pokemon_id')
                ->where('hp_stats.stat_name', '=', 'hp');
        })
        ->orderBy('hp_stats.base_stat', $direction)
        ->select('pokemon.*');
})
```

## Testing Configuration

- Uses PHPUnit for testing
- Test database: SQLite in-memory (`:memory:`)
- Test suites: `Unit` and `Feature`
- Run all tests: `composer test` or `php artisan test`
- Run specific test: `php artisan test tests/Feature/ExampleTest.php`

## Environment

- Default database: SQLite (`database/database.sqlite`)
- Queue driver: Database
- Session driver: Database
- Cache driver: Database (Redis in production)
- Production: MySQL/PostgreSQL + Redis (see `DOCKER.md`)