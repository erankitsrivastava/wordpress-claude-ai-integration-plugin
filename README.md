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

## 📖 Documentation

See the [plugin README](claude-ai-integration/README.md) for detailed documentation.

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

## 📝 License

GPL v2 or later

## 👨‍💻 Author

HaxCode - [GitHub](https://github.com/erankitsrivastava)
