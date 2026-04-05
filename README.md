# Serinity Web

Symfony web recreation of the original JavaFX desktop forum module from serinity-desktop.

## Scope Rebuilt from Desktop Version

- Forum threads with full CRUD
- Categories (parent/child) with admin management
- Nested replies on thread detail
- Interactions: upvote, downvote, follow
- Notifications and read/unread handling
- Forum statistics dashboard
- Search and category filtering
- Optional translation and summarization integrations

## Tech Stack

- Symfony 7.4
- Doctrine ORM + Doctrine Migrations
- MySQL
- Twig + Bootstrap 5
- Symfony Forms + Validator
- Doctrine Fixtures

## Project Structure

- src/Entity: Doctrine entities mapped from Java models
- src/Service: business logic adapted from Java services
- src/Controller: web controllers mapped from JavaFX action logic
- src/Form: form definitions for thread, reply, category workflows
- templates: Twig views replacing JavaFX FXML screens

## Prerequisites

- PHP 8.2+
- Composer
- MySQL 8+
- Symfony CLI (recommended)

## Installation

1. Install dependencies:

```bash
composer install
```

2. Configure environment variables in .env:

- DATABASE_URL
- APP_SECRET
- API_TRANSLATE_KEY (optional)

Default database URL in this project:

```env
DATABASE_URL="mysql://root:@127.0.0.1:3306/serinity_web?serverVersion=mariadb-10.4.0"
```

3. Create and migrate database:

```bash
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
```

4. Load sample data fixtures:

```bash
php bin/console doctrine:fixtures:load --no-interaction
```

5. Start the application:

```bash
symfony server:start
```

## Default Fixture Users

- admin@serinity.local / admin123 (ROLE_ADMIN)
- alice@serinity.local / alice123 (ROLE_USER)
- bob@serinity.local / bob123 (ROLE_USER)

Note: Current build uses a fallback current-user resolver for local development if no Symfony login is active.

## Hardcoded Current User ID (Forum-only mode)

If you want to change the hardcoded fallback current user (similar to the desktop module), edit:

- File: src/Service/CurrentUserService.php
- Line: 10
- Constant: FALLBACK_USER_ID

Example:

```php
private const FALLBACK_USER_ID = 1;
```

Change `1` to any existing user id from your database.

## Feature Mapping (Desktop -> Web)

- ForumPostsController -> ForumController feed pages
- AddThreadController -> ThreadManageController new/edit + ForumThreadType
- ThreadDetailController -> ForumController detail + Reply form + interactions
- ForumBackofficeController + AddCategoryController -> AdminController + CategoryType
- NotificationsPanelController -> NotificationController + notifications page
- StatisticsController -> Admin statistics page

## Optional Integrations

- Translation: set API_TRANSLATE_KEY to use Gemini API
- Summarization: run local Python service on http://localhost:5000/summarize
- Moderation: Purgomalum API is called automatically in thread/reply creation

## Validation Commands

```bash
php bin/console about
php bin/console doctrine:schema:validate
php bin/console lint:twig templates
php bin/console lint:container
```

## Notes

- Image upload currently stores files in public/uploads.
- PDF export service scaffold exists and can be extended with a concrete PDF library.
- Security and role access rules can be hardened further in config/packages/security.yaml.
