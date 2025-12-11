# Claude AI Integration for WordPress

A powerful WordPress plugin that integrates Anthropic's Claude AI to help you generate, create, and modify WordPress content with ease.

## Features

✨ **Content Generation**
- Generate complete blog posts and pages from a simple topic
- Multiple content lengths (short, medium, long)
- Various writing tones (professional, casual, friendly, formal, etc.)
- SEO-friendly HTML output

🎨 **Content Modification**
- Modify existing content with natural language instructions
- Improve, expand, or refine your posts
- Maintain HTML formatting and structure

🤖 **AI-Powered Assistance**
- Generate compelling titles automatically
- Create engaging excerpts
- Meta box integration in post/page editor
- Real-time content generation

⚙️ **Flexible Configuration**
- Multiple Claude models supported
- Adjustable parameters (max tokens, temperature)
- Secure API key storage
- Simple settings interface

## Installation

1. Download the plugin
2. Upload the `claude-ai-integration` folder to `/wp-content/plugins/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Configure your API key in Settings

## Configuration

### Getting Your API Key

1. Visit [Anthropic Console](https://console.anthropic.com/)
2. Sign up or log in to your account
3. Navigate to API keys section
4. Create a new API key
5. Copy the key and paste it in the plugin settings

### Plugin Settings

Navigate to **Claude AI > Settings** in your WordPress admin:

- **API Key**: Your Anthropic API key (required)
- **Model**: Choose from available Claude models
  - Claude Sonnet 4.5 (Latest - Recommended)
  - Claude 3.7 Sonnet
  - Claude 3.5 Sonnet
  - Claude 3 Opus
  - Claude 3 Haiku
- **Max Tokens**: Maximum length of generated content (256-8192)
- **Temperature**: Creativity level (0-2, higher = more creative)

## Usage

### Content Generator

1. Go to **Claude AI > Content Generator**
2. Select your content type (Post or Page)
3. Enter your topic or subject
4. Choose content length and tone
5. Click "Generate Content"
6. Review the generated content
7. Save as draft or copy to clipboard

### Post/Page Editor Integration

When editing a post or page, you'll find the **Claude AI Assistant** meta box in the sidebar:

**Generate Title**
- Automatically creates a compelling title based on your content
- Updates the post title field

**Generate Excerpt**
- Creates a concise summary of your content
- Updates the excerpt field

**Modify Content**
- Enter natural language instructions (e.g., "Make it more engaging", "Add bullet points")
- Transforms your content according to your instructions
- Preserves HTML formatting

## Examples

### Content Generation Examples

**Topic**: "Benefits of meditation for stress relief"
**Length**: Medium
**Tone**: Professional

The AI will generate a complete, well-structured article with:
- Compelling headline
- Introduction
- Multiple sections with subheadings
- Conclusion
- Proper HTML formatting

### Content Modification Examples

- "Make this more conversational"
- "Add bullet points to summarize key takeaways"
- "Expand the introduction with more details"
- "Simplify the language for a general audience"
- "Add a call-to-action at the end"

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Valid Anthropic API key
- Active internet connection

## Support & Documentation

- **GitHub**: [https://github.com/erankitsrivastava/haxcode-shopify-landing-page](https://github.com/erankitsrivastava/haxcode-shopify-landing-page)
- **Issues**: Report bugs or request features on GitHub

## Security

- API keys are stored securely in WordPress options
- All AJAX requests are nonce-protected
- User capability checks on all sensitive operations
- Input sanitization and output escaping

## Changelog

### Version 1.0.0
- Initial release
- Content generation for posts and pages
- Content modification features
- Title and excerpt generation
- Meta box integration
- Settings page with full configuration

## License

GPL v2 or later

## Credits

Developed by HaxCode
Powered by Anthropic's Claude AI

---

**Note**: This plugin requires an active Anthropic API subscription. API usage is subject to Anthropic's pricing and terms of service.
