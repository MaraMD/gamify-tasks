# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a **gamified task management system** built with Laravel 12. Users complete tasks to earn XP for their character, with a level-up system based on accumulated experience.

## Development Commands

### Setup & Installation
```bash
# Initial setup (includes dependencies, migrations, seeds, assets)
composer setup

# Or manual setup:
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
```

### Running the Application
```bash
# Development mode (runs server + queue + logs + vite concurrently)
composer dev

# Or individually:
php artisan serve              # Server only
php artisan queue:listen       # Queue worker
php artisan pail              # Real-time logs
npm run dev                   # Vite dev server
```

### Database Operations
```bash
# Fresh migration with sample data
php artisan migrate:fresh --seed

# Create infrastructure tables (sessions, cache, queues)
php artisan session:table
php artisan cache:table
php artisan queue:table
php artisan queue:failed-table
```

### Testing
```bash
# Run all tests
composer test
# Or: php artisan test

# Run specific test
php artisan test --filter=TestClassName

# Run with coverage (requires xdebug)
php artisan test --coverage
```

### Code Quality
```bash
# Laravel Pint (code style fixer)
./vendor/bin/pint

# Pint with specific preset
./vendor/bin/pint --preset laravel
```

## Architecture & Data Model

### Core Models & Relationships

**User → Character (1:1)**
- Each user has one main character created via `getOrCreateMainCharacter()`
- Character tracks XP and derives level from it (level = xp/100 + 1)
- Character model uses an accessor `getLevelAttribute()` for computed level

**User → Task (1:N)**
- Tasks belong to users and track completion status
- Task statuses: `STATUS_PENDING = 0`, `STATUS_COMPLETED = 1`

**Task → TaskCompletion (1:N)**
- Completion history tracks when tasks were completed and points awarded
- Created via transaction when task is marked complete
- Indexed by `[user_id, completed_at]` for weekly queries

### Key Business Logic

**Task Completion Flow** (`TaskController::complete`)
1. Task status updated to COMPLETED
2. TaskCompletion record created with points_awarded
3. Character XP incremented by task points
4. All wrapped in DB transaction for atomicity

**XP System**
- Characters have `xp` field (unsigned integer, default 0)
- Level calculation: `intdiv(xp, 100) + 1`
- Level is a computed attribute, NOT stored in database
- XP is added via `Character::increment('xp', $points)`

### Scopes & Query Patterns

**Task Scopes:**
- `scopePendingToday()` - Today's pending tasks ordered by difficulty
- `scopeForUser($userId)` - Filter by user

**TaskCompletion Queries:**
- Weekly completions use `whereBetween('completed_at', [$start, $end])`
- Always eager load `->with('task')` to avoid N+1 queries

### Authorization Pattern

Controllers use a helper pattern for auth:
```php
private function currentUserId(): int {
    return auth()->id() ?? 1;  // Falls back to demo user (id=1)
}

private function authorizeTask(Task $task): void {
    abort_if($task->user_id !== $this->currentUserId(), 403);
}
```

This allows the app to work without authentication for demo purposes.

### Database Indexes

Important indexes for performance:
- `tasks`: `[user_id, due_date]` for today/upcoming queries
- `tasks`: `[status, completed_at]` for completion filtering
- `task_completions`: `[user_id, completed_at]` for weekly stats

## Routes Structure

```
GET  /                      → Redirects to /tasks/today
GET  /character             → Show character profile
POST /character             → Update character name/avatar
GET  /tasks/today           → Today's pending tasks
GET  /tasks/completed-week  → This week's completions
POST /tasks/{task}/complete → Mark task complete (triggers XP)
      /tasks                → Resource routes (index, store, edit, update, destroy)
```

## Seeding & Test Data

`DatabaseSeeder` creates:
- Demo user: `demo@example.com` / `password` (id=1)
- Character: "Héroe" with 120 XP (level 2)
- 12 random tasks + 2 specific examples
- TaskCompletions for any pre-completed tasks

## Important Patterns & Conventions

### Mass Assignment Security
- Task model excludes `user_id` from `$fillable` - always set via relationship:
  ```php
  $user->tasks()->create($data);  // ✓ Correct
  Task::create(['user_id' => ...]);  // ✗ Avoid
  ```

### Character Creation
- Always use `User::getOrCreateMainCharacter()` instead of direct queries
- This ensures character exists and avoids race conditions

### Transaction Usage
- Task completion MUST be wrapped in transaction (XP + completion record)
- Prevents partial updates if one operation fails

### Validation Rules
- Task difficulty: `in:1,2,3` (though DB supports 1-5)
- Task points: `min:1` (unsigned small int)
- Dates use Carbon for timezone-aware operations

## Common Tasks

### Adding New Task Scopes
Add to `Task` model following existing pattern:
```php
public function scopeYourScope($query) {
    return $query->where(...);
}
```

### Modifying XP Calculation
Update `Character::getLevelAttribute()` - level is derived, not stored.

### Adding Achievements/Badges
Create new model with `user_id` FK, add relationship to User model.

### Testing Task Completion
Use transactions in tests and check:
1. Task status changed
2. TaskCompletion created
3. Character XP increased
4. All in same transaction

## Environment Notes

- Uses SQLite for testing (`:memory:`)
- Demo setup expects MySQL/MariaDB on port 3306
- Default Laragon config: root user, no password
- Vite runs on default port for asset compilation
