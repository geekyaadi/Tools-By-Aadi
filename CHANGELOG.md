# CHANGELOG

## [v1.0.0] - 2026-08-26

### 🚀 Initial Rebranded Launch of Soniji Auto Blogging
- **Multi-API Key Pool & Rotation**: Add unlimited Gemini & OpenAI API keys. Automatic failover switches keys immediately on quota or rate-limit errors.
- **Mid-Step Resume Pipeline**: Multi-step generation pipeline (Title ➔ Article ➔ Tags ➔ Meta ➔ FAQ ➔ Thumbnail). If an API call fails mid-way, it resumes from the exact failed step without losing work.
- **Automated Drip Scheduler**: Set "publish X posts per day" with niche rotation, WP-Cron automation, and real-time live queue tracking.
- **Bulk Article Planner**: Generate up to 50 articles in one click with custom topic seeds, title choices, and batch processing.
- **Dual Thumbnail Engines**:
  - **AI Image Generator**: Gemini-powered featured images with custom style reference upload.
  - **Canvas Graphics Generator (Local GD)**: Fast, high-res canvas generator with gradient palettes, solid colors, custom background uploads, and contrast-matched typography (Outfit, Poppins, Roboto).
- **Auto-Internal Linking**: Automatically link relevant keywords in new articles to your existing published posts.
- **Google Indexing & IndexNow**: Automatically submit new posts to Google Search Console and IndexNow for instant indexing upon publish.
- **AI Article Rewriter & Content Freshener**: Rewrite, update, and improve existing published articles directly from the WordPress dashboard.
- **Bulk Language Translator**: Translate published posts into 10+ languages with sequential queue handling.
- **Dynamic FAQ & Schema Generator**: Auto-generate interactive FAQ accordions and inject Google JSON-LD Schema markup.
