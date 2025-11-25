## 100.4.0

- Added storefront setting to choose the widget rendering mode (Knockout vs Hyvä/Alpine fallback)
- Ensured only one renderer executes at a time to prevent Cloudflare "sitekey object" errors
- Added dedicated fallback container class (`cf-turnstile-manual`) so Turnstile auto-render does not attach to Hyvä widgets
- Improved frontend/admin CSS and JS to respect the selected rendering mode

## 100.3.0

- Allow adding a captcha to the native newsletter subscription block ([2](https://github.com/Pixel-Open/magento-cloudflare-turnstile/issues/2))

## 100.2.8

- Fix multiple widget renderer on ajax login
- Fix ajax login event when cart is not subscribed

## 100.2.7

- Added "flexible" size option ([@MikeWilkie](https://github.com/MikeWilkie))

## 100.2.6

- Widget added during checkout login when email exists ([14](https://github.com/Pixel-Open/magento-cloudflare-turnstile/issues/14))

## 100.2.5

- Fix redirect URI error when no referer

## 100.2.4

- Fix configuration scope ([@Igmar95](https://github.com/Igmar95))

## 100.2.3

- Avoid PHP error when module action class does not exist

## 100.2.2

- Invalid response message updated
- Send product to friend form protection

## 100.2.1

- Fix review form persistence
- Simpler way to add an action to validate
- New data persistor interface

## 100.2.0

- Admin "login" and "reset password" forms validation
- Validation added on guest checkout login form
- Widget size configuration
- French translation
- Fix API request on each page with authentication popup
- Fix widget resetting on ajax call

## 100.1.2

- Config path updated
- CSP Whitelist added
- Fix form validator when Turnstile is disabled

## 100.1.1

- Fix review data persistor
- Do not use deprecated inheritance in controllers
- Module dependencies updated

## 100.1.0

- First stable release
