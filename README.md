# Magento Cloudflare Turnstile

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.0-green)](https://php.net/)
[![Minimum Magento Version](https://img.shields.io/badge/magento-%3E%3D%202.4.4-green)](https://business.adobe.com/products/magento/magento-commerce.html)
[![GitHub release](https://img.shields.io/github/v/release/Pixel-Open/magento-cloudflare-turnstile)](https://github.com/Pixel-Open/magento-cloudflare-turnstile/releases)

## Presentation

[Turnstile](https://developers.cloudflare.com/turnstile/) is Cloudflare's smart CAPTCHA alternative. The module allows Turnstile to protect your Magento OpenSource or Adobe Commerce forms.

![Cloudflare Turnstile](screenshot.png)

### Frontend Forms

- Contact
- Login
- Register
- Reset password
- Review

### Admin Forms

- Login
- Reset password

## Requirements

- Magento >= 2.4.4
- PHP >= 8.0

## Installation

```
composer require pixelopen/magento-cloudflare-turnstile
```

## Configuration

### Disable all Magento Captcha

*Stores > Configuration > Customers > Customer Configuration > CAPTCHA*

- **Enable CAPTCHA on Storefront**: no

*Stores > Configuration > Security > Google reCAPTCHA Storefront > Storefront*

- **Enable for Customer Login**: no
- **Enable for Forgot Password**: no
- **Enable for Create New Customer Account**: no
- **Enable for Contact Us**: no
- **Enable for Product Review**: no

*Stores > Configuration > Security > Google reCAPTCHA Admin Panel > Admin Panel*

- **Enable for Login**: no
- **Enable for Forgot Password**: no

### Enable Cloudflare Turnstile

*Stores > Configuration > Services > Cloudflare Turnstile*

**Settings**

- **Enable**: enable Cloudflare Turnstile
- **Sitekey**: the sitekey given for the site in your Cloudflare dashboard
- **Secret key**: the secret key given for the site in your Cloudflare dashboard

**Storefront**

- **Theme**: the Turnstile theme (auto, light or dark)
- **Size**: the widget size (compact or normal)
- **Forms to validate**: the frontend forms where a Turnstile validation is required

**Admin Panel**

- **Theme**: the Turnstile theme (auto, light or dark)
- **Size**: the widget size (compact or normal)
- **Forms to validate**: the admin forms where a Turnstile validation is required

### Override default config

You can change the theme and size values for a specific form in the layout:

```xml
<?xml version="1.0"?>
<!-- layout/customer_account_login.xml -->
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:View/Layout/etc/page_configuration.xsd">
    <body>
        <referenceContainer name="form.additional.info">
            <block name="pixel.open.cloudflare.turnstile.login">
                <action method="setSize">
                    <argument name="size" xsi:type="string">compact</argument>
                </action>
                <action method="setTheme">
                    <argument name="theme" xsi:type="string">dark</argument>
                </action>
            </block>
        </referenceContainer>
    </body>
</page>
```

### Testing

Use the following sitekeys and secret keys for testing purposes:

**Sitekey**

| Sitekey                  | Description                     |
|--------------------------|---------------------------------|
| 1x00000000000000000000AA | Always passes                   |
| 2x00000000000000000000AB | Always blocks                   |
| 3x00000000000000000000FF | Forces an interactive challenge |

**Secret key**

| Secret key                          | Description                          |
|-------------------------------------|--------------------------------------|
| 1x0000000000000000000000000000000AA | Always passes                        |
| 2x0000000000000000000000000000000AA | Always fails                         |
| 3x0000000000000000000000000000000AA | Yields a "token already spent" error |
