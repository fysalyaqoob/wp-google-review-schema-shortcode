# Google Review Schema Shortcode for WordPress

A lightweight and dynamic WordPress shortcode plugin that generates structured data using the Google Places API. Perfect for enhancing your SEO with up-to-date reviews and star ratings in the SERPs.

---

## 🚀 Features

- ✅ Shortcode to dynamically generate `LocalBusiness` schema with real Google reviews
- ✅ Optional `WebApplication` schema output for your online service
- ✅ Automatically fetches rating, review count, and address from Google Places API
- ✅ Outputs structured data in valid JSON-LD format in the page footer
- ✅ Supports customizable overrides via shortcode attributes

---

## 🔧 Usage

### Basic Usage

```
[google_review_schema place_id="YOUR_GOOGLE_PLACE_ID"]
```

### With WebApplication Schema

```
[google_review_schema place_id="YOUR_GOOGLE_PLACE_ID" webapp="true"]
```

### Optional Attributes

| Attribute   | Required? | Description                          |
|-------------|-----------|--------------------------------------|
| `place_id`  | ✅ Yes    | Google Place ID for the location     |
| `webapp`    | ❌ No     | Set to `true` to include WebApplication schema |
| `name`      | ❌ No     | Override business name               |
| `description`| ❌ No    | Custom description for schemas       |
| `phone`     | ❌ No     | Override phone number                |
| `street`    | ❌ No     | Street address override              |
| `locality`  | ❌ No     | City override                        |
| `region`    | ❌ No     | Province/state override              |
| `postal`    | ❌ No     | Postal/ZIP code                      |
| `country`   | ❌ No     | Country code (e.g., CA, US)          |

---

## 🧪 Validation

You can test the structured data using:
- [Google Rich Results Test](https://search.google.com/test/rich-results)
- [Schema.org Validator](https://validator.schema.org/)

---

## 🔐 API Key Setup

Add the following line to your `wp-config.php`:

```php
define('GOOGLE_PLACES_API_KEY', 'your-google-api-key-here');
```

> ⚠️ Do not hardcode your API key in the plugin for security reasons.

---

## 📁 Folder Structure

```
wp-google-review-schema-shortcode/
├── plugin.php
├── LICENSE
├── readme.md
├── includes/
│   └── shortcode-handler.php
├── assets/
```

---

## 📄 License

Licensed under the GPLv2 or later. See `LICENSE` file for details.
