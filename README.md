# domain-catcher
Order domain when it becomes available for registration via WebSupport API v1 https://rest.websupport.sk/docs/v1.intro

# Installation
1. Run `composer install`
2. Copy `.env.example` to `.env`
3. Replace API identifier, secret and user ID in `.env` with your own from https://admin.websupport.sk/en/auth/security-settings

# Usage
Set cron to URL `http://localhost:8000/cron/?domain=YOUR_TRACKED_DOMAIN`

# Xdebug
Append query param `XDEBUG_SESSION=1` to the URL to enable debug mode.
