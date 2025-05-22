# domain-catcher
Order domain when it becomes available for registration via WebSupport API v1 https://rest.websupport.sk/docs/v1.intro

# Installation
1. Run `composer install`
2. Copy `.env.example` to `.env`
3. Replace API identifier, secret and user ID in `.env` with your own from https://admin.websupport.sk/en/auth/security-settings
4. Set `DRY_RUN` to `true` if you want to test the script without actually ordering the domain.

# Usage
Set cron to URL `http://localhost:8000/cron/?domains[]=test.sk&domains[]=test2.sk&...`

# Xdebug
Append query param `XDEBUG_SESSION=1` to the URL to enable debug mode.
