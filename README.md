# Claude AI Integration for WordPress

A powerful WordPress plugin that integrates Anthropic's Claude AI to help you generate, create, and modify WordPress content with ease.

## 🚀 Quick Start

### Installation

**Option 1: Direct Download (Recommended)**
1. Download the latest release ZIP from the [Releases page](https://github.com/erankitsrivastava/wordpress-claude-ai-integration-plugin/releases)
2. Go to WordPress Admin → Plugins → Add New → Upload Plugin
3. Upload the ZIP file and click "Install Now"
4. Activate the plugin

**Option 2: Manual Installation from Source**
1. Clone or download this repository
2. Navigate to the repository directory
3. Run `./build.sh` to create the plugin ZIP file (or manually ZIP the `claude-ai-integration` folder)
4. Upload `claude-ai-integration.zip` to WordPress via Plugins → Add New → Upload Plugin
5. Activate the plugin

**Option 3: Manual Installation via FTP**
1. Clone or download this repository
2. Copy the `claude-ai-integration` folder to your WordPress `/wp-content/plugins/` directory
3. Go to WordPress Admin → Plugins and activate "Claude AI Integration"

### Setup

1. Get your API key from [Anthropic Console](https://console.anthropic.com/)
2. Go to WordPress Admin → Claude AI → Settings
3. Enter your Anthropic API key
4. Configure your preferred settings (model, temperature, etc.)
5. Start generating amazing content!

## 🔑 Integration Guide

### Step 1: Get Your Anthropic API Key

1. Visit [Anthropic Console](https://console.anthropic.com/)
2. Sign up or log in to your account
3. Navigate to **API Keys** section
4. Click **Create Key** to generate a new API key
5. Copy the API key (it will only be shown once)

**Note:** Keep your API key secure and never share it publicly.

### Step 2: Configure Plugin Settings

Once the plugin is installed and activated:

1. Go to **WordPress Admin Dashboard**
2. Click **Claude AI** in the left sidebar
3. Click **Settings** submenu
4. Enter your configuration:

#### API Configuration Options:

| Setting | Description | Default | Recommended |
|---------|-------------|---------|-------------|
| **API Key** | Your Anthropic API key | None | Required |
| **Model** | Claude model to use | Claude Sonnet 4.5 | Sonnet for balanced quality/speed |
| **Max Tokens** | Maximum response length | 4096 | 4096 for posts, 2048 for titles |
| **Temperature** | Creativity level (0-2) | 1.0 | 1.0 for balanced output |

#### Available Models:

- **Claude Sonnet 4.5** (Latest): Best balance of intelligence, speed, and cost
- **Claude 3.7 Sonnet**: Previous generation with excellent performance
- **Claude 3.5 Sonnet**: Older but reliable model
- **Claude 3 Opus**: Most capable but slower and more expensive
- **Claude 3 Haiku**: Fastest and most affordable, good for simple tasks

5. Click **Save Changes**

### Step 3: Verify Configuration

After saving your settings:

1. Go to **Claude AI → Content Generator**
2. Try generating a test post with a simple topic like "Benefits of Morning Exercise"
3. If content generates successfully, your integration is complete!

If you see errors:
- Verify your API key is correct
- Check your Anthropic account has API credits
- Ensure your server can make outbound HTTPS connections

## 📘 How to Use

### Method 1: Content Generator Page

The Content Generator is a standalone page for creating complete posts or pages from scratch.

#### Creating New Content:

1. Go to **Claude AI → Content Generator**
2. Fill in the form:
   - **Content Type**: Choose Post or Page
   - **Topic**: Enter the topic (e.g., "10 Tips for Remote Work Productivity")
   - **Length**: Select Short (300-500 words), Medium (700-1000 words), or Long (1500-2000 words)
   - **Tone**: Choose Professional, Casual, Friendly, or Informative
3. Click **Generate Content**
4. Wait for Claude to generate your content (15-30 seconds)
5. Review the generated content in the preview
6. Click **Create Post** or **Create Page** to publish

**Use Cases:**
- Quickly create multiple blog posts on different topics
- Generate landing pages for marketing campaigns
- Create educational content or tutorials
- Develop product descriptions or service pages

**Example Topics:**
```
✓ "How to Start a WordPress Blog in 2025"
✓ "Complete Guide to Social Media Marketing"
✓ "10 Healthy Breakfast Recipes Under 15 Minutes"
✓ "Understanding Cryptocurrency for Beginners"
✓ "Best Practices for Remote Team Management"
```

### Method 2: Post/Page Editor Integration

Claude AI adds an assistant sidebar to all post and page editors for real-time content enhancement.

#### Accessing the Editor Assistant:

1. Go to **Posts → Add New** or **Pages → Add New**
2. Look for the **Claude AI Assistant** meta box in the right sidebar
3. Use the available tools to enhance your content

#### Available Tools:

##### 1. Generate Title

Automatically creates SEO-friendly, engaging titles based on your content.

**How to use:**
1. Write or paste your content in the editor
2. Click **Generate Title** in the Claude AI Assistant box
3. The title field will be automatically filled with AI-generated title
4. Edit if needed or generate again for alternatives

**Example:**
- Content about healthy eating → "10 Simple Strategies to Transform Your Diet and Boost Energy"
- Content about WordPress → "Mastering WordPress: A Complete Guide for Beginners"

##### 2. Generate Excerpt

Creates compelling 2-3 sentence summaries of your content for search engine results and social sharing.

**How to use:**
1. Write your content in the editor
2. Click **Generate Excerpt**
3. The excerpt field will be automatically populated
4. Review and adjust if needed

**Example:**
For a blog post about productivity, generates:
> "Discover proven strategies to boost your productivity and achieve more in less time. This comprehensive guide covers time management techniques, focus strategies, and tools that successful professionals use daily. Transform your workflow and accomplish your goals faster."

##### 3. Modify Content

Transforms existing content based on your instructions using natural language commands.

**How to use:**
1. Write or paste content in the editor
2. In the **Modify Content** textarea, enter your instruction
3. Click **Modify Content**
4. Review the AI-modified content
5. Confirm to replace or cancel to keep original

**Modification Instructions Examples:**

```
Content Enhancement:
✓ "Make this more engaging and conversational"
✓ "Add humor and personality to this text"
✓ "Make this sound more professional"
✓ "Simplify this for a beginner audience"

Formatting Changes:
✓ "Add bullet points to highlight key information"
✓ "Break this into shorter paragraphs"
✓ "Add subheadings to organize the content"
✓ "Convert this into a numbered step-by-step guide"

Content Expansion:
✓ "Add examples to illustrate each point"
✓ "Expand this section with more details"
✓ "Add statistics and data to support the claims"
✓ "Include actionable tips at the end"

Style Changes:
✓ "Rewrite this in a storytelling format"
✓ "Make this more persuasive for sales"
✓ "Transform this into FAQ format"
✓ "Rewrite from first-person to third-person perspective"

SEO Optimization:
✓ "Optimize this for the keyword 'digital marketing'"
✓ "Add relevant keywords naturally throughout"
✓ "Make this more SEO-friendly without keyword stuffing"
```

### Method 3: Bulk Content Creation Workflow

For creating multiple related posts efficiently:

1. Use **Content Generator** to create base articles on related topics
2. Edit each post in the **Post Editor**
3. Use **Modify Content** to customize tone or add specific details
4. Generate **SEO-optimized titles** and **compelling excerpts**
5. Publish or schedule posts

**Example Workflow:**
```
Topic Series: "Home Office Setup Guide"

1. Generate base posts:
   - "Essential Equipment for Your Home Office"
   - "Ergonomic Tips for Remote Workers"
   - "Creating a Productive Workspace at Home"

2. Customize each:
   - Modify tone to match your brand
   - Add personal experiences
   - Include product recommendations

3. Optimize for SEO:
   - Generate keyword-rich titles
   - Create compelling excerpts
   - Review and publish
```

## 💡 Best Practices

### Content Generation Tips

1. **Be Specific with Topics**: Instead of "Marketing", use "Email Marketing Strategies for Small E-commerce Businesses"
2. **Choose Appropriate Length**: Short for news/updates, Medium for blog posts, Long for comprehensive guides
3. **Match Tone to Audience**: Professional for B2B, Casual for lifestyle blogs, Friendly for community content
4. **Review and Edit**: Always review AI-generated content and add your unique perspective
5. **Add Personal Touch**: Include personal anecdotes, case studies, or specific examples

### Modification Instructions Tips

1. **Be Clear and Specific**: "Add 3 examples" vs. "Add some examples"
2. **One Instruction at a Time**: Multiple modifications work better in sequence
3. **Provide Context**: "Add bullet points for the benefits section" vs. just "Add bullet points"
4. **Test Different Approaches**: Try variations if first result isn't perfect

### API Usage Optimization

1. **Model Selection**:
   - Use **Haiku** for simple tasks (titles, excerpts, short edits)
   - Use **Sonnet** for most content generation (best balance)
   - Use **Opus** for complex, high-quality long-form content

2. **Token Management**:
   - Lower max_tokens for titles/excerpts (512-1024)
   - Medium for blog posts (2048-4096)
   - Higher for comprehensive guides (4096-8192)

3. **Temperature Settings**:
   - 0.5-0.7: Factual, consistent content (technical writing, documentation)
   - 0.8-1.0: Balanced creativity (blog posts, articles)
   - 1.0-1.5: Creative content (storytelling, marketing copy)

## 📦 Plugin Structure

```
claude-ai-integration/
├── claude-ai-integration.php (Main plugin file)
├── includes/
│   ├── class-claude-ai-activator.php
│   ├── class-claude-ai-deactivator.php
│   ├── class-claude-ai-core.php
│   └── class-claude-ai-api.php
├── admin/
│   ├── class-claude-ai-admin.php
│   ├── class-claude-ai-content-generator.php
│   └── partials/
│       ├── settings-page.php
│       └── content-generator-page.php
├── assets/
│   ├── css/
│   │   └── claude-ai-admin.css
│   └── js/
│       └── claude-ai-admin.js
└── README.md
```

## ✨ Features

- **Content Generation**: Create complete blog posts and pages from a simple topic
- **Content Modification**: Edit and improve existing content with AI
- **Title Generation**: Generate compelling titles automatically
- **Excerpt Generation**: Create engaging excerpts
- **Multiple Models**: Support for all Claude models
- **Flexible Settings**: Customize tokens, temperature, and more
- **Editor Integration**: Meta box in post/page editor for real-time assistance
- **Bulk Content Creation**: Efficiently create multiple related posts
- **SEO Optimization**: Generate SEO-friendly titles and meta descriptions

## 📖 Documentation

See the [plugin README](claude-ai-integration/README.md) for detailed documentation.

## 🎯 Use Cases & Examples

### 1. Blog Content Creation

**Scenario:** You run a food blog and need to publish 3 posts per week.

**Workflow:**
```
Monday: Generate 3 base posts
- "Easy Weeknight Dinner Recipes for Busy Families"
- "Healthy Meal Prep Ideas for Weight Loss"
- "Budget-Friendly Grocery Shopping Tips"

Tuesday-Wednesday: Customize content
- Add personal cooking experiences
- Modify tone to be more conversational
- Include family anecdotes

Thursday: SEO optimization
- Generate catchy titles
- Create compelling excerpts
- Add relevant keywords naturally

Friday: Review and schedule for the week
```

### 2. E-commerce Product Descriptions

**Scenario:** You need product descriptions for 50+ items.

**Process:**
1. Use Content Generator with short length
2. Topic format: "Product description for [Product Name] - [Key Features]"
3. Professional tone for B2B, Casual for consumer products
4. Modify to add specifications and benefits
5. Generate SEO-friendly titles

**Example:**
```
Topic: "Product description for Wireless Bluetooth Headphones - Noise Canceling, 30-hour battery"
Output: Professional description highlighting features, benefits, and use cases
```

### 3. Service Business Marketing

**Scenario:** Digital agency needs landing pages for different services.

**Steps:**
1. Generate long-form content for each service
2. Topics: "Professional [Service Name] Services - Benefits and Process"
3. Modify to add client testimonials and case studies
4. Generate persuasive titles and meta descriptions
5. Create consistent tone across all pages

### 4. Educational Content & Tutorials

**Scenario:** Creating an online course with supporting blog content.

**Implementation:**
1. Generate comprehensive guides (long length)
2. Convert to step-by-step format using Modify Content
3. Add examples and exercises
4. Generate engaging titles for each lesson
5. Create summaries/excerpts for course catalog

### 5. Content Refresh & SEO Update

**Scenario:** Updating old blog posts for better SEO and engagement.

**Process:**
1. Open existing post in editor
2. Use Modify Content with instructions:
   - "Update statistics with 2025 data"
   - "Add current best practices"
   - "Optimize for keyword: [target keyword]"
3. Generate new SEO-friendly title
4. Update excerpt for better CTR

## 🔧 Advanced Configuration

### Custom Workflows with Different Models

Create model presets for different content types:

**For Quick Tasks:**
- Model: Claude 3 Haiku
- Max Tokens: 1024
- Temperature: 0.7
- Use for: Titles, excerpts, short edits

**For Blog Posts:**
- Model: Claude Sonnet 4.5
- Max Tokens: 4096
- Temperature: 1.0
- Use for: Standard blog content

**For Premium Content:**
- Model: Claude 3 Opus
- Max Tokens: 8192
- Temperature: 1.2
- Use for: Comprehensive guides, whitepapers

### Integration with Other Plugins

**SEO Plugins (Yoast, Rank Math):**
1. Generate content with Claude AI
2. Use SEO plugin to analyze
3. Modify content based on SEO recommendations
4. Regenerate title/excerpt if needed

**Page Builders (Elementor, Gutenberg):**
1. Generate HTML content with Claude AI
2. Paste into page builder
3. Apply styling and layout
4. Add images and media

**Multilingual Plugins (WPML, Polylang):**
1. Generate content in primary language
2. Use Modify Content: "Translate this to Spanish maintaining formatting"
3. Create posts in each language

## ❓ Frequently Asked Questions

### General Questions

**Q: Do I need to pay for the Claude API?**
A: Yes, you need an Anthropic account with API credits. Anthropic offers pay-as-you-go pricing. Check [Anthropic Pricing](https://www.anthropic.com/pricing) for current rates.

**Q: How much does it cost per post?**
A: Costs vary by model and content length:
- Short post (500 words): ~$0.01-0.05
- Medium post (1000 words): ~$0.03-0.10
- Long post (2000 words): ~$0.05-0.20

**Q: Is the generated content unique?**
A: Yes, Claude generates original content each time. However, always review and add your unique perspective for best results.

**Q: Can I use this for commercial websites?**
A: Yes, the plugin is GPL licensed and can be used for commercial projects. Review Anthropic's terms of service for API usage.

**Q: Does this work with Gutenberg and Classic Editor?**
A: Yes, the plugin works with both WordPress editors. The Claude AI Assistant meta box appears in the sidebar of both editors.

### Technical Questions

**Q: What are the server requirements?**
A:
- PHP 7.4 or higher
- WordPress 5.0 or higher
- Outbound HTTPS connections enabled
- cURL extension enabled

**Q: Is my API key stored securely?**
A: Yes, the API key is stored in WordPress options table and only transmitted via HTTPS to Anthropic's API.

**Q: Can I use this on localhost/development sites?**
A: Yes, as long as your development environment can make outbound HTTPS connections to api.anthropic.com.

**Q: Does this work with multisite?**
A: Yes, but you'll need to configure API settings separately for each site in the network.

**Q: What if the API request fails?**
A: The plugin displays user-friendly error messages. Common issues:
- Invalid API key: Double-check your key in settings
- Network timeout: Increase PHP max_execution_time
- Rate limiting: Wait a moment and try again
- No API credits: Add credits to your Anthropic account

### Content Questions

**Q: Can it generate content in other languages?**
A: Yes, specify the language in your topic or use Modify Content to translate. Example: "Write a blog post about healthy eating in French"

**Q: How do I maintain my brand voice?**
A: Use Modify Content with specific instructions about tone, style, and terminology. You can also add brand guidelines to each prompt.

**Q: Can it write in specific formats (listicles, how-tos, reviews)?**
A: Yes, include the format in your topic. Examples:
- "Write a listicle: 10 Best Productivity Apps for 2025"
- "Create a how-to guide: Setting Up a Home Gym"
- "Write a product review: iPhone 15 Pro Max"

**Q: Will Google penalize AI-generated content?**
A: Google focuses on content quality and value, not how it's created. Always review, edit, and add unique insights to AI-generated content for best SEO results.

**Q: Can I save custom prompts or templates?**
A: Currently, the plugin uses predefined prompts. For custom workflows, use the Modify Content feature with specific instructions.

## ❗ Troubleshooting

### "No valid plugins were found" Error

If you get this error during installation, it usually means WordPress received an incorrectly structured ZIP file. To fix this:

1. **Don't download the repository ZIP directly from GitHub** - This creates a ZIP with the wrong structure
2. **Use the release ZIP** from the [Releases page](https://github.com/erankitsrivastava/wordpress-claude-ai-integration-plugin/releases), OR
3. **Build the plugin yourself**:
   ```bash
   git clone https://github.com/erankitsrivastava/wordpress-claude-ai-integration-plugin.git
   cd wordpress-claude-ai-integration-plugin
   ./build.sh
   ```
   Then upload the generated `claude-ai-integration.zip` file

4. **Manual installation**: Copy the `claude-ai-integration` folder directly to `/wp-content/plugins/`

The issue occurs because WordPress expects a ZIP containing a plugin directory with a main PHP file, not a repository structure.

### API Connection Errors

**Error:** "Claude API key is not configured"
- Go to Claude AI → Settings and enter your API key
- Ensure you copied the complete key without extra spaces
- Save settings and try again

**Error:** "Invalid API key" or "Authentication failed"
- Verify your API key at [Anthropic Console](https://console.anthropic.com/)
- Generate a new API key if needed
- Check that your Anthropic account is active

**Error:** "Request timeout" or "Connection timed out"
- Your server may be blocking outbound HTTPS connections
- Contact your hosting provider to allow connections to api.anthropic.com
- Increase PHP max_execution_time in php.ini or wp-config.php:
  ```php
  set_time_limit(120);
  ```

**Error:** "Rate limit exceeded"
- You're making too many requests in a short time
- Wait a few minutes before trying again
- Consider upgrading your Anthropic API tier for higher limits

**Error:** "Insufficient credits"
- Your Anthropic account is out of API credits
- Add credits at [Anthropic Console](https://console.anthropic.com/)
- Check your current balance and usage

### Content Generation Issues

**Problem:** Generated content is too short/long
- Adjust Max Tokens in Settings (Claude AI → Settings)
- Use different length options in Content Generator
- Be more specific in your topic description

**Problem:** Content quality is inconsistent
- Try different temperature settings (lower = more consistent)
- Use more specific, detailed topics
- Experiment with different models (Opus for highest quality)
- Add specific requirements in your instructions

**Problem:** Content not matching desired tone
- Explicitly specify tone in topic: "Write in casual, friendly tone..."
- Use Modify Content to adjust tone after generation
- Provide examples of desired style in your instructions

**Problem:** Generated content has formatting issues
- The plugin generates HTML - ensure your editor supports it
- Check if your theme/plugins are stripping HTML tags
- Switch between Visual/Text editor modes to verify

### Editor Integration Issues

**Problem:** Claude AI Assistant box not appearing
- Verify plugin is activated
- Check if you're editing a post or page (not custom post types)
- Clear browser cache and refresh page
- Check browser console for JavaScript errors

**Problem:** Buttons not responding
- Clear browser cache
- Disable other plugins temporarily to check for conflicts
- Check browser console for errors
- Ensure JavaScript is enabled in your browser

**Problem:** Generated content not inserting into editor
- Check browser console for errors
- Verify you have permission to edit posts
- Try switching between Visual and Text editor modes
- Refresh the page and try again

### Performance Optimization

**Slow content generation:**
1. Switch to faster model (Haiku for quick tasks)
2. Reduce Max Tokens setting
3. Check your server's internet connection speed
4. Verify PHP max_execution_time is sufficient

**High API costs:**
1. Use Haiku model for simple tasks (cheaper)
2. Reduce Max Tokens to only what you need
3. Lower temperature for more focused output (less trial needed)
4. Generate content in batches rather than many small requests

## 🔧 Development

This plugin is built with:
- WordPress Plugin API
- Anthropic Claude API
- Modern JavaScript (ES6+)
- Responsive CSS

### Building the Plugin

To create a distributable ZIP file:

```bash
./build.sh
```

This creates `claude-ai-integration.zip` ready for WordPress installation.

## 📋 Quick Reference

### Common Actions

| Task | Location | Steps |
|------|----------|-------|
| Configure API | Claude AI → Settings | Enter API key, select model, adjust settings |
| Generate new post | Claude AI → Content Generator | Enter topic, select options, click Generate |
| Modify existing content | Post Editor → Claude AI Assistant | Enter instruction, click Modify Content |
| Generate title | Post Editor → Claude AI Assistant | Click Generate Title |
| Generate excerpt | Post Editor → Claude AI Assistant | Click Generate Excerpt |

### Keyboard Shortcuts & Tips

- **Fast Content Creation**: Use Content Generator for multiple posts in one session
- **Quick Edits**: Use Modify Content with short, specific instructions
- **SEO Optimization**: Generate titles and excerpts after finalizing content
- **Batch Processing**: Generate multiple posts, then customize each in editor

### Best Topic Formats

```
✓ Good: "Complete Guide to Starting a Podcast in 2025 - Equipment, Software, and Marketing"
✗ Avoid: "Podcasting"

✓ Good: "How to Create Instagram Reels That Get 10K+ Views"
✗ Avoid: "Social Media Tips"

✓ Good: "Keto Meal Prep for Beginners: 7-Day Plan with Shopping List"
✗ Avoid: "Healthy Eating"
```

### Plugin Support

- **Documentation**: This README
- **Issues**: [GitHub Issues](https://github.com/erankitsrivastava/wordpress-claude-ai-integration-plugin/issues)
- **API Support**: [Anthropic Support](https://support.anthropic.com/)
- **WordPress Support**: [WordPress.org Forums](https://wordpress.org/support/)

## 🔐 Security & Privacy

### Data Handling

- **API Key Storage**: Encrypted in WordPress database
- **Content Transmission**: All data sent via HTTPS
- **No Data Retention**: Content is not stored by Anthropic after processing (per their API policy)
- **Privacy**: Only content you explicitly send is processed by Claude API

### Best Practices

1. **Protect Your API Key**:
   - Never commit to version control
   - Don't share in screenshots or demos
   - Regenerate if accidentally exposed

2. **Content Review**:
   - Always review AI-generated content before publishing
   - Add fact-checking for technical or medical content
   - Verify statistics and claims

3. **Compliance**:
   - Follow Anthropic's terms of service
   - Comply with content regulations in your jurisdiction
   - Add appropriate disclaimers for AI-assisted content if required

## 🚀 Roadmap

Planned features for future releases:

- [ ] Custom prompt templates
- [ ] Content history and versioning
- [ ] Bulk content generation UI
- [ ] Integration with popular page builders
- [ ] Custom post type support
- [ ] Scheduled content generation
- [ ] Multi-language interface
- [ ] Content analytics and insights
- [ ] Team collaboration features
- [ ] Advanced SEO optimization tools

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

GPL v2 or later

## 👨‍💻 Author

HaxCode - [GitHub](https://github.com/erankitsrivastava)
