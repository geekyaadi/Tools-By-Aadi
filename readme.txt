=== Tools By Aadi ===
Contributors: Anand Soni, Aadi
Tags: ai, gemini, auto-post, seo, content-generator
Requires at least: 6.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Auto-generate, rewrite, translate, and index SEO-optimized blog posts using Google Gemini & OpenAI API with multi-key rotation.

== Description ==

**Tools By Aadi** is an all-in-one AI-powered content automation suite for WordPress. It leverages the power of Google Gemini API and OpenAI ChatGPT to generate, schedule, translate, rewrite, and index high-quality SEO blog posts automatically.

### 🌟 Key Features

* 🔑 **Multi-API Key Pool & Rotation** — Add unlimited Gemini & OpenAI API keys. Automatic failover switches keys immediately on quota or rate-limit errors.
* ⏸ **Mid-Step Resume Pipeline** — Multi-step generation pipeline (Title ➔ Article ➔ Tags ➔ Meta ➔ FAQ ➔ Thumbnail). If an API call fails mid-way, it resumes from the exact failed step without losing work.
* ⏱ **Key Auto-Reset** — Exhausted API keys automatically reset after a configurable interval.
* 📅 **Automated Drip Scheduler** — Set "publish X posts per day" with niche rotation, WP-Cron automation, and real-time live queue tracking.
* 📋 **Bulk Article Planner** — Generate up to 50 articles in one click with custom topic seeds, title choices, and batch processing.
* 🖼️ **Dual Thumbnail Engines**:
  - **AI Image Generator**: Gemini-powered featured images with custom style reference upload.
  - **Title to Image (Local GD)**: Fast, high-res canvas generator with gradient palettes, solid colors, custom background uploads, and contrast-matched typography (Outfit, Poppins, Roboto).
* 🔗 **Auto-Internal Linking** — Automatically link relevant keywords in new articles to your existing published posts.
* 🚀 **Google Indexing API (GSC)** — Automatically submit new posts to Google Search Console for instant indexing upon publish, plus manual batch URL submission tool.
* 📝 **AI Article Rewriter & Content Freshener** — Rewrite, update, and improve existing published articles directly from the WordPress dashboard.
* 🌍 **Bulk Language Translator** — Translate published posts into 10+ languages with sequential queue handling.
* ❓ **Dynamic FAQ & Schema Generator** — Auto-generate interactive FAQ accordions and inject Google JSON-LD Schema markup.
* ✍️ **Custom Prompt Templates** — Full control over prompts for Titles, Articles, Meta Descriptions, Tags, and FAQs with live prefilled defaults.
* 🚫 **Content & Word Blacklist** — Exclude specific words or phrases from appearing in generated titles, text, or tags.
* 🧹 **Automated Cache Purging** — Instant automatic cache flushing (PHP OPcache, WP Object Cache, LiteSpeed, WP Rocket, W3TC, Autoptimize) on version updates.

== Installation ==

1. Upload the `tools-by-aadi` directory to your `/wp-content/plugins/` folder (or install via **Plugins ➔ Add New ➔ Upload Plugin**).
2. Activate **Tools By Aadi** through the **Plugins** screen in WordPress.
3. Navigate to **Tools By Aadi ➔ Settings** and add your free Google Gemini API key (from [Google AI Studio](https://aistudio.google.com/app/apikey)).
4. Start generating articles via **Tools By Aadi ➔ Generate Post** or set up automatic publishing in **Bulk Planner** & **Scheduler**.

== Frequently Asked Questions ==

= Where do I get a free Gemini API key? =
You can get a free API key instantly at [Google AI Studio](https://aistudio.google.com/app/apikey).

= How does Multi-Key rotation work? =
If your active API key hits a rate limit or daily quota, the plugin instantly marks it as exhausted and switches to the next available key in your pool. Generation continues seamlessly.

= Does it support Google Indexing API (GSC)? =
Yes! You can paste or upload your Google Cloud Service Account JSON key in **Settings ➔ Indexing**, and enable auto-pinging on post publish or submit URLs manually.

== Third-Party Services ==

This plugin relies on external third-party APIs for AI content generation, image creation, language translation, and search indexing:

* **Google Gemini API** (by Google LLC)
  - Purpose: Generates blog posts, titles, meta tags, FAQs, and AI featured images.
  - Terms of Service: https://ai.google.dev/terms
  - Privacy Policy: https://policies.google.com/privacy

* **OpenAI ChatGPT API** (by OpenAI LLC)
  - Purpose: Alternative AI engine for article generation, rewriting, and translation.
  - Terms of Service: https://openai.com/policies/terms-of-use/
  - Privacy Policy: https://openai.com/policies/privacy-policy/

* **Google Search Console & Indexing API** (by Google LLC)
  - Purpose: Submits published post URLs to Google Search index.
  - Terms of Service: https://developers.google.com/terms
  - Privacy Policy: https://policies.google.com/privacy

* **Wikipedia API** (by Wikimedia Foundation)
  - Purpose: Contextual search lookup for high-authority E-E-A-T outbound linking.
  - Terms of Service: https://foundation.wikimedia.org/wiki/Policy:Terms_of_Use
  - Privacy Policy: https://foundation.wikimedia.org/wiki/Policy:Privacy_policy

== Changelog ==

= 1.0.0 =
* Initial rebranded launch of Tools By Aadi content automation suite.
* Integrated Multi-API Key Pool & Rotation for Google Gemini & OpenAI keys.
* Integrated Google Search Console & IndexNow automated submission.
* Integrated local Canvas graphics generator and Gemini AI image engines.
* Added Drip Scheduler, Bulk Article Planner, Article Rewriter, and Multi-Language Translation.
