# StatScoreApp – Installation & Usage Guide

Recruitment task prototype for StatScore: a Laravel application demonstrating handling of football (soccer) events (fouls, goals), real-time statistics updates, and Server-Sent Events (SSE) streaming.

## Requirements

- Docker + Docker Desktop (recommended via Laravel Sail)
- Git
- (Optional) Local Composer – if you prefer not to rely only on the container

## Quick Start (Docker / Sail)

1. Clone the repository:
   ```bash
   git clone https://github.com/RafalSchneider/statScore.git
   cd StatScoreApp
   ```
2. Create the `.env` file:
   ```bash
   copy .env.example .env  
   ```
3. (Optional) Change the application port in `.env` via `APP_PORT=8080` if port 80 is busy.
4. Start containers:
   ```bash
   docker compose up -d
   ```
5. Install dependencies & prepare the app (inside the container):
   ```bash
   docker compose exec laravel.test composer install
   docker compose exec laravel.test php artisan key:generate
   docker compose exec laravel.test php artisan migrate

   ```
6. Application available at: `http://localhost` (or chosen port). API lives under `/api` prefix.
7. (Queue) Start a worker to process event jobs:
   ```bash
   docker compose exec laravel.test php artisan queue:listen --queue=events --tries=1
   # Alternatively: docker compose exec laravel.test php artisan horizon
   ```

## API Endpoints

Prefix: `/api`

- POST `/api/event` – register an event (foul / goal)
- GET `/api/events` – list events (filters: `match_id`, `team_id`, `type`, `player`, `per_page`)
- GET `/api/events/{id}` – single event
- GET `/api/statistics?match_id=...&team_id=...` – stats for one team
- GET `/api/statistics?match_id=...` – stats for all teams in a match
- GET `/api/events-stream` – SSE stream of last and new events


## Tests
Run all tests inside Docker:
```bash
docker compose exec laravel.test php artisan test
```

### AI Disclaimer
Tests were generated / assisted by AI.