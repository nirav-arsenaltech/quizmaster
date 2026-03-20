# 🧠 QuizMaster — Laravel 12 Production Quiz Application

A fully production-ready quiz application built with **Laravel 12**, **Tailwind CSS v4**, **Pest**, and **SQLite** (or any database).

---

## ✅ Full Laravel Structure

This is a **complete Laravel application** — every file is included. Just `composer install` and go.

```
quizmaster/
├── app/
│   ├── Console/
│   ├── Exceptions/
│   │   └── Handler.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── QuizController.php
│   │   │   │   └── QuestionController.php
│   │   │   ├── Auth/
│   │   │   │   ├── AuthenticatedSessionController.php
│   │   │   │   └── RegisteredUserController.php
│   │   │   ├── Controller.php
│   │   │   ├── DashboardController.php
│   │   │   ├── LeaderboardController.php
│   │   │   ├── QuizController.php
│   │   │   └── ResultsController.php
│   │   ├── Middleware/
│   │   │   ├── AdminMiddleware.php
│   │   │   └── HandleInertiaRequests.php
│   │   └── Requests/
│   │       ├── Admin/
│   │       │   ├── StoreQuizRequest.php
│   │       │   └── StoreQuestionRequest.php
│   │       └── SubmitQuizRequest.php
│   ├── Models/
│   │   ├── Answer.php
│   │   ├── AttemptAnswer.php
│   │   ├── Question.php
│   │   ├── Quiz.php
│   │   ├── QuizAttempt.php
│   │   └── User.php
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   └── Services/
│       └── QuizService.php
├── bootstrap/
│   ├── app.php
│   ├── providers.php
│   └── cache/
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── cache.php
│   ├── database.php
│   ├── filesystems.php
│   ├── logging.php
│   ├── mail.php
│   ├── queue.php
│   ├── sanctum.php
│   └── session.php
├── database/
│   ├── factories/
│   │   ├── AnswerFactory.php
│   │   ├── QuestionFactory.php
│   │   ├── QuizFactory.php
│   │   └── UserFactory.php
│   ├── migrations/
│   │   ├── ..._create_users_table.php         (+ sessions + password_reset_tokens)
│   │   ├── ..._create_cache_jobs_table.php    (+ cache_locks + jobs + job_batches + failed_jobs)
│   │   ├── ..._create_quizzes_table.php
│   │   └── ..._create_quiz_tables.php         (questions + answers + quiz_attempts + attempt_answers)
│   └── seeders/
│       └── DatabaseSeeder.php
├── public/
│   ├── index.php
│   ├── .htaccess
│   └── robots.txt
├── resources/
│   ├── css/app.css
│   ├── js/
│   │   ├── app.js
│   │   └── bootstrap.js
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php       (dark sidebar layout)
│       │   └── guest.blade.php     (centered auth layout)
│       ├── auth/
│       │   ├── login.blade.php
│       │   └── register.blade.php
│       ├── dashboard/index.blade.php
│       ├── quiz/
│       │   ├── index.blade.php
│       │   └── take.blade.php
│       ├── results/
│       │   ├── index.blade.php
│       │   └── show.blade.php
│       ├── leaderboard/index.blade.php
│       └── admin/
│           ├── quizzes/
│           │   ├── index.blade.php
│           │   └── form.blade.php
│           └── questions/
│               ├── index.blade.php
│               └── form.blade.php
├── routes/
│   ├── web.php
│   └── console.php
├── storage/
│   ├── app/public/
│   ├── framework/{cache,sessions,views}/
│   └── logs/
├── tests/
│   ├── Feature/QuizTest.php   (30 tests)
│   ├── Pest.php
│   └── TestCase.php
├── .env.example
├── .gitignore
├── artisan
├── composer.json
├── package.json
├── phpunit.xml
└── vite.config.js
```

---

## 🚀 Setup (5 steps)

```bash
# 1. Install PHP dependencies
composer install

# 2. Install JS dependencies
npm install

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Database setup (SQLite — zero config)
touch database/database.sqlite
php artisan migrate
php artisan db:seed

# 5. Build assets & serve
npm run build
php artisan serve
```

**Visit:** http://localhost:8000

---

## 👤 Demo Accounts

| Role  | Email                      | Password |
|-------|----------------------------|----------|
| Admin | admin@quizmaster.test      | password |
| User  | user@quizmaster.test       | password |

---

## 🗄️ Database Schema

| Table            | Purpose                                        |
|------------------|------------------------------------------------|
| `users`          | Auth + `is_admin` flag                         |
| `quizzes`        | Title, time limit, max attempts, settings      |
| `questions`      | Body, order, explanation (soft deletes)        |
| `answers`        | Body, `is_correct`, order                      |
| `quiz_attempts`  | Attempt per user, score, question_order (JSON) |
| `attempt_answers`| One row per question per attempt               |
| `sessions`       | DB-backed sessions                             |
| `cache`          | DB-backed cache                                |
| `jobs`           | Queue                                          |

---

## 🧭 Routes

| Method      | URI                                         | Description                  |
|-------------|---------------------------------------------|------------------------------|
| GET         | `/`                                         | Redirect to dashboard        |
| GET/POST    | `/login`                                    | Login                        |
| GET/POST    | `/register`                                 | Register                     |
| POST        | `/logout`                                   | Logout                       |
| GET         | `/dashboard`                                | Analytics dashboard          |
| GET         | `/quizzes`                                  | List quizzes                 |
| POST        | `/quizzes/{quiz}/start`                     | Start attempt                |
| GET         | `/attempts/{attempt}`                       | Take quiz                    |
| POST        | `/attempts/{attempt}/submit`                | Submit answers               |
| GET         | `/results`                                  | All my results               |
| GET         | `/results/{attempt}`                        | Result detail + review       |
| GET         | `/leaderboard`                              | Top 10 players               |
| GET         | `/admin/quizzes`                            | Manage quizzes               |
| GET/POST    | `/admin/quizzes/create`                     | Create quiz                  |
| GET/PUT     | `/admin/quizzes/{quiz}/edit`                | Edit quiz                    |
| DELETE      | `/admin/quizzes/{quiz}`                     | Archive quiz (soft delete)   |
| GET         | `/admin/quizzes/{quiz}/questions`           | Manage questions             |
| GET/POST    | `/admin/quizzes/{quiz}/questions/create`    | Add question                 |
| GET/PUT     | `/admin/quizzes/{quiz}/questions/{q}/edit`  | Edit question                |
| DELETE      | `/admin/quizzes/{quiz}/questions/{q}`       | Delete question              |

---

## 🧪 Run Tests

```bash
php artisan test
# or
./vendor/bin/pest
```

**30 tests** covering:

- Auth (guest redirect, register, login)
- Quiz listing and start
- Scoring (10/10, 0/10, partial)
- AttemptAnswer persistence
- 3-attempt limit (per user, not global)
- Independent session counts
- Timer + `timed_out` flag
- Results access control (owner vs. other)
- `buildReview` structure
- Anti-spoofing (cross-question answer rejection)
- Question randomisation count
- Leaderboard sort order
- Admin access control (403 for non-admins)
- Admin CRUD (create, soft-delete)

---

## ⚙️ Key Design Decisions

**Service Layer** — `QuizService` owns all business logic. Controllers are thin HTTP adapters.

**Anti-spoofing** — `Question::ownsAnswer()` verifies submitted answer IDs belong to the correct question before grading.

**Idempotent submit** — Submitting a completed attempt is a no-op (returns immediately).

**Timer persistence** — JS stores deadline in `sessionStorage` as a Unix timestamp. Page refresh recalculates remaining time from the deadline, not from zero.

**Soft deletes** — Quizzes and questions use `SoftDeletes` to preserve historical attempt data.

**DB-backed sessions** — Sessions stored in the `sessions` table, not files.
