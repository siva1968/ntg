# NerdsIQ AI Assistant WordPress Plugin

> Secure WordPress integration with AWS Q Business chatbot for NerdsToGo team members

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![WordPress](https://img.shields.io/badge/wordpress-5.8+-green.svg)
![PHP](https://img.shields.io/badge/php-7.4+-purple.svg)
![License](https://img.shields.io/badge/license-GPL--2.0+-red.svg)

## 🚀 At a Glance

**What is this?** An AI chatbot for your WordPress website that answers questions using your company knowledge base.

**Who is it for?** Your team members who need quick answers (only logged-in WordPress users can access it).

**What do I need?**
- WordPress 5.8+ website ✅
- Amazon AWS account ✅
- 30 minutes to set up ✅

**Is it hard to install?** No! We have a [Quick Start Guide](QUICK-START.md) for beginners.

**Is it secure?** Yes! All credentials are encrypted, and only authorized users can access the chatbot.

**How much does it cost?** The plugin is free. AWS Q Business has usage-based pricing (~$106-112/month for 5 users).

---

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [AWS Setup Guide](#aws-setup-guide) ⭐
- [Usage](#usage)
- [Security](#security)
- [Architecture](#architecture)
- [Development](#development)
- [Troubleshooting](#troubleshooting)
- [License](#license)

## 📚 Documentation

### 👋 New to WordPress or AWS?
- **[⭐ QUICK START GUIDE](QUICK-START.md)** - **START HERE!** Beginner-friendly guide for non-technical users

### Detailed Guides
- **[AWS Configuration Guide](AWS-CONFIGURATION.md)** - Complete AWS setup (534 lines, detailed)
- **[Installation Guide](INSTALL.md)** - Technical installation instructions
- **[README](README.md)** - This file (complete reference documentation)

### 🤔 Which Guide Should I Use?

| Your Experience | Recommended Guide | Time Needed |
|----------------|-------------------|-------------|
| 😊 Never used WordPress plugins or AWS | [QUICK-START.md](QUICK-START.md) | 30 min |
| 🤔 Used WordPress, new to AWS | [AWS-CONFIGURATION.md](AWS-CONFIGURATION.md) | 20 min |
| 😎 Familiar with both | [INSTALL.md](INSTALL.md) | 10 min |
| 🚀 Just want all technical details | Keep reading below | - |

## 🎯 Overview

NerdsIQ AI Assistant is a professional WordPress plugin that integrates AWS Q Business chatbot functionality directly into your WordPress site. It provides secure, role-based access to an AI-powered knowledge assistant without requiring end users to have AWS credentials.

### Key Benefits

- ✅ **Seamless Single Sign-On**: Users authenticate with WordPress credentials only
- ✅ **Secure by Design**: All AWS credentials encrypted, server-side API integration
- ✅ **Fully Customizable**: Configure appearance, behavior, and access without code changes
- ✅ **Mobile Responsive**: Works perfectly on all devices
- ✅ **Enterprise-Grade**: Built for production use with comprehensive logging and monitoring

## ✨ Features

### User Features
- Real-time AI-powered chat interface
- Conversation history and persistence
- Source citations with clickable links
- Mobile-optimized responsive design
- Suggested questions to get started
- Markdown formatting support

### Admin Features
- **Complete Admin Panel** with tabbed interface
  - General Settings (AWS configuration)
  - Access Control (role-based permissions)
  - Appearance (colors, branding, positioning)
  - Behavior (rate limiting, features)
  - Advanced (custom CSS/JS, debugging)

- **Analytics Dashboard**
  - Total conversations and messages
  - Unique users and engagement metrics
  - Average response times
  - User activity tracking

- **Monitoring & Logging**
  - Usage logs with filtering
  - Conversation history viewer
  - Error tracking and diagnostics
  - System status monitoring

### Security Features
- Encrypted credential storage (AES-256)
- Role-based access control
- Page-level restrictions
- Rate limiting (hourly/daily)
- IP-based DDoS protection
- PII redaction in logs
- XSS/CSRF protection

## 📋 Requirements

### WordPress Environment
- **WordPress**: 5.8 or higher
- **PHP**: 7.4 or higher (8.0+ recommended)
- **MySQL**: 5.7 or higher
- **HTTPS**: Required for production

### PHP Extensions
- `curl` - For API requests
- `json` - For data processing
- `openssl` - For encryption
- `mbstring` - For string handling

### AWS Requirements
- Active AWS account
- AWS Q Business application configured
- S3 bucket with knowledge base data
- IAM credentials with appropriate permissions

### Required IAM Permissions
```json
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Action": [
        "qbusiness:ChatSync",
        "qbusiness:ListMessages",
        "qbusiness:GetConversation",
        "qbusiness:ListConversations"
      ],
      "Resource": "arn:aws:qbusiness:*:*:application/*"
    },
    {
      "Effect": "Allow",
      "Action": [
        "s3:GetObject",
        "s3:ListBucket"
      ],
      "Resource": [
        "arn:aws:s3:::your-knowledge-base-bucket",
        "arn:aws:s3:::your-knowledge-base-bucket/*"
      ]
    }
  ]
}
```

## 🚀 Installation

### Method 1: Manual Installation

1. **Download the Plugin**
   ```bash
   git clone https://github.com/nerdstogo/nerdsiq-ai-assistant.git
   cd nerdsiq-ai-assistant
   ```

2. **Install Dependencies**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. **Upload to WordPress**
   - Compress the `nerdsiq-ai-assistant` folder to `nerdsiq-ai-assistant.zip`
   - Navigate to WordPress Admin → Plugins → Add New → Upload Plugin
   - Upload the ZIP file and click "Install Now"
   - Click "Activate Plugin"

### Method 2: FTP Installation

1. **Upload via FTP**
   ```bash
   # After installing dependencies
   scp -r nerdsiq-ai-assistant/ user@yourserver:/path/to/wordpress/wp-content/plugins/
   ```

2. **Activate in WordPress**
   - Go to WordPress Admin → Plugins
   - Find "NerdsIQ AI Assistant"
   - Click "Activate"

### Post-Installation

After activation, the plugin will:
- Create necessary database tables
- Set default configuration options
- Add custom user capabilities
- Display activation success message

## ⚙️ Configuration

### Quick Configuration Overview

After installing the plugin, you'll need to configure AWS credentials. This requires:

1. **AWS Access Key ID** - From IAM user
2. **AWS Secret Access Key** - From IAM user (encrypted when saved)
3. **AWS Region** - Where your Q Business app is deployed
4. **Q Business Application ID** - From AWS Q Business console

> 📖 **Need detailed AWS setup instructions?**
> See the complete **[AWS Configuration Guide](AWS-CONFIGURATION.md)** for step-by-step instructions with screenshots and troubleshooting.

### Step 1: AWS Setup

1. **Navigate to Settings**
   - WordPress Admin → NerdsIQ → Settings
   - Go to "General Settings" tab

2. **Enter AWS Credentials**
   - **AWS Access Key ID**: Your IAM access key
   - **AWS Secret Access Key**: Your IAM secret key (will be encrypted)
   - **AWS Region**: Select your Q Business region (e.g., us-east-1)
   - **Q Business Application ID**: Your application ID from AWS console

3. **Test Connection**
   - Click "Test Connection" button
   - Verify successful connection (green status)
   - Check latency is acceptable

> ⚠️ **First time setting up AWS?**
> Follow our **[AWS Configuration Guide](AWS-CONFIGURATION.md)** for complete instructions on creating IAM users, policies, and getting your credentials.

### Step 2: Access Control

1. **Go to Access Control Tab**

2. **Configure Display Pages**
   - Select "All pages" or specific pages
   - Use URL patterns for advanced control (e.g., `/internal/*`)

3. **Set Allowed Roles**
   - Check roles that should have chatbot access
   - Default: Administrator and Editor
   - Custom roles are also supported

4. **Optional: Whitelist/Blacklist**
   - Add specific user emails to whitelist
   - Add specific user emails to blacklist

### Step 3: Customize Appearance

1. **Go to Appearance Tab**

2. **Widget Position**
   - Choose: Bottom Right or Bottom Left

3. **Colors & Branding**
   - Primary Color (button, header)
   - User Message Color
   - AI Message Color
   - Use color picker for easy selection

4. **Welcome Message**
   - Customize greeting text
   - Add suggested questions

5. **Dimensions**
   - Widget Width: 300-800px
   - Widget Height: 400-900px
   - Border Radius, Shadow Depth

### Step 4: Configure Behavior

1. **Go to Behavior Tab**

2. **Conversation Settings**
   - Enable/disable conversation history
   - Set conversation timeout
   - Maximum messages per conversation

3. **Rate Limiting**
   - Hourly limit (default: 50 messages)
   - Daily limit (default: 250 messages)
   - Customize rate limit message

4. **Features**
   - Toggle typing indicator
   - Toggle source citations
   - Toggle follow-up suggestions

### Step 5: Advanced Settings (Optional)

1. **Custom CSS**
   - Add custom styles to match your brand
   - Example:
     ```css
     .nerdsiq-chat-window {
         font-family: 'Your Custom Font', sans-serif;
     }
     ```

2. **Debug Mode**
   - Enable for troubleshooting
   - Logs written to WordPress debug.log
   - Remember to disable in production

3. **Performance**
   - Enable caching for faster responses
   - Set cache duration
   - Configure API timeouts and retries

## 🔐 AWS Setup Guide

### Complete AWS Configuration

For detailed instructions on setting up AWS credentials, see the **[AWS Configuration Guide](AWS-CONFIGURATION.md)**.

This guide includes:

- ✅ Step-by-step IAM user creation
- ✅ IAM policy configuration with examples
- ✅ How to generate and secure access keys
- ✅ Finding your Q Business Application ID
- ✅ Configuring the plugin in WordPress
- ✅ Testing your connection
- ✅ Troubleshooting common issues
- ✅ Security best practices
- ✅ Quick reference cards

### Quick AWS Checklist

Before configuring the plugin, gather these four pieces of information:

```
☐ AWS Access Key ID: AKIA________________
☐ AWS Secret Access Key: ____________________
☐ AWS Region: ___________ (e.g., us-east-1)
☐ Q Business Application ID: ________-____-____-____-____________
```

### Required IAM Permissions

Your IAM user needs this policy:

```json
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Action": [
        "qbusiness:ChatSync",
        "qbusiness:ListMessages",
        "qbusiness:GetConversation",
        "qbusiness:ListConversations"
      ],
      "Resource": "arn:aws:qbusiness:*:*:application/*"
    },
    {
      "Effect": "Allow",
      "Action": [
        "s3:GetObject",
        "s3:ListBucket"
      ],
      "Resource": [
        "arn:aws:s3:::your-bucket-name",
        "arn:aws:s3:::your-bucket-name/*"
      ]
    }
  ]
}
```

> 📚 **See full AWS setup guide**: [AWS-CONFIGURATION.md](AWS-CONFIGURATION.md)

## 📖 Usage

### For End Users

1. **Starting a Chat**
   - Look for the "Ask NerdsIQ" button (bottom right/left)
   - Click to open the chat window
   - Type your question and press Enter or click Send

2. **Using Suggested Questions**
   - Click any suggested question button
   - Question will be automatically sent

3. **Viewing Sources**
   - AI responses include source citations
   - Click source links to view original documents

4. **New Conversation**
   - Click the "+" icon in header
   - Confirms before resetting

5. **Mobile Use**
   - Chat opens in full screen on mobile
   - All features work on touchscreens

### For Administrators

1. **Monitoring Usage**
   - WordPress Admin → NerdsIQ → Analytics
   - View real-time statistics
   - Track engagement metrics

2. **Viewing Conversations**
   - WordPress Admin → NerdsIQ → Conversations
   - Browse all user conversations
   - Search and filter options
   - Export for analysis

3. **Checking Logs**
   - WordPress Admin → NerdsIQ → Usage Logs
   - Filter by date, user, action
   - Download logs as CSV

4. **System Health**
   - WordPress Admin → NerdsIQ → System Status
   - Check AWS connection
   - Verify PHP requirements
   - View diagnostic information

## 🔒 Security

### Data Protection

- **Encryption at Rest**: AWS credentials encrypted using AES-256
- **Encryption in Transit**: All API calls use HTTPS/TLS
- **No Client-Side Credentials**: Users never see AWS keys
- **Session Management**: WordPress session handling
- **PII Redaction**: Automatic redaction in logs

### Access Controls

1. **Authentication Layer**: Must be logged into WordPress
2. **Role-Based Access**: Configurable role permissions
3. **Page Restrictions**: Limit where chatbot appears
4. **Rate Limiting**: Prevent abuse
5. **IP Rate Limiting**: DDoS protection

### Security Best Practices

✅ **DO:**
- Use strong AWS IAM credentials
- Rotate credentials regularly
- Enable WordPress 2FA for admins
- Keep WordPress and plugin updated
- Use HTTPS in production
- Review access logs regularly

❌ **DON'T:**
- Share AWS credentials
- Disable rate limiting
- Allow anonymous access
- Store credentials in version control
- Use debug mode in production

### Compliance Features

- **GDPR**: Data export, deletion, consent tracking
- **Data Retention**: Configurable log retention policies
- **Audit Trail**: Complete activity logging
- **Privacy**: User conversation anonymization options

## 🏗️ Architecture

### High-Level Design

```
┌─────────────────────────────────────────────┐
│          WordPress Frontend                  │
│  ┌─────────────────────────────────────┐   │
│  │   Chat Widget (HTML/CSS/JS)         │   │
│  │   - User Interface                  │   │
│  │   - Message Display                 │   │
│  │   - Input Handling                  │   │
│  └─────────────────────────────────────┘   │
└──────────────────┬──────────────────────────┘
                   │ AJAX
                   ▼
┌─────────────────────────────────────────────┐
│       WordPress Backend (PHP)                │
│  ┌─────────────────────────────────────┐   │
│  │   Public Class                       │   │
│  │   - AJAX Handlers                   │   │
│  │   - Access Control                  │   │
│  │   - Rate Limiting                   │   │
│  └──────────────┬──────────────────────┘   │
│                 │                            │
│  ┌─────────────▼──────────────────────┐   │
│  │   AWS Client Class                  │   │
│  │   - API Integration                 │   │
│  │   - Request/Response Handling       │   │
│  └──────────────┬──────────────────────┘   │
└─────────────────┼──────────────────────────┘
                  │ HTTPS/TLS
                  ▼
┌─────────────────────────────────────────────┐
│            AWS Q Business API                │
│  ┌─────────────────────────────────────┐   │
│  │   Q Business Service                 │   │
│  │   - Natural Language Processing     │   │
│  │   - Document Search                 │   │
│  │   - Response Generation             │   │
│  └──────────────┬──────────────────────┘   │
└─────────────────┼──────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────┐
│          S3 Knowledge Base                   │
│   - NerdsToGo Documentation                  │
│   - Synced from Google Drive                 │
└─────────────────────────────────────────────┘
```

### Database Schema

**Table: `wp_nerdsiq_conversations`**
- Stores conversation metadata
- Links to WordPress users
- Tracks conversation status

**Table: `wp_nerdsiq_messages`**
- Stores all messages (user & assistant)
- Includes response times
- Contains source citations (JSON)

**Table: `wp_nerdsiq_usage_logs`**
- Logs user actions
- Tracks widget opens, closes, etc.
- Optional IP logging

**Table: `wp_nerdsiq_errors`**
- Error tracking
- Stack traces for debugging
- Resolution status

### File Structure

```
nerdsiq-ai-assistant/
├── nerdsiq-ai-assistant.php    # Main plugin file
├── uninstall.php                # Cleanup on uninstall
├── composer.json                # PHP dependencies
├── README.md                    # Documentation
│
├── includes/                    # Core functionality
│   ├── class-nerdsiq-ai-assistant.php
│   ├── class-nerdsiq-loader.php
│   ├── class-nerdsiq-i18n.php
│   ├── class-nerdsiq-activator.php
│   ├── class-nerdsiq-deactivator.php
│   │
│   ├── api/                     # AWS integration
│   │   └── class-nerdsiq-aws-client.php
│   │
│   ├── database/                # Database management
│   │   └── class-nerdsiq-database.php
│   │
│   ├── security/                # Security features
│   │   ├── class-nerdsiq-security.php
│   │   ├── class-nerdsiq-access-control.php
│   │   └── class-nerdsiq-rate-limiter.php
│   │
│   └── logging/                 # Logging system
│       └── class-nerdsiq-logger.php
│
├── admin/                       # Admin interface
│   ├── class-nerdsiq-admin.php
│   ├── css/
│   │   └── nerdsiq-admin.css
│   ├── js/
│   │   └── nerdsiq-admin.js
│   └── partials/
│       ├── nerdsiq-admin-settings.php
│       ├── nerdsiq-admin-analytics.php
│       ├── nerdsiq-admin-conversations.php
│       ├── nerdsiq-admin-usage-logs.php
│       └── nerdsiq-admin-system-status.php
│
└── public/                      # Public-facing
    ├── class-nerdsiq-public.php
    ├── css/
    │   └── nerdsiq-public.css
    ├── js/
    │   └── nerdsiq-public.js
    └── partials/
        └── nerdsiq-chat-widget.php
```

## 🛠️ Development

### Local Development Setup

1. **Clone Repository**
   ```bash
   git clone https://github.com/nerdstogo/nerdsiq-ai-assistant.git
   cd nerdsiq-ai-assistant
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Set Up Local WordPress**
   - Install WordPress locally (XAMPP, LocalWP, Docker, etc.)
   - Symlink or copy plugin to `wp-content/plugins/`

4. **Enable Debug Mode**
   - Edit `wp-config.php`:
     ```php
     define('WP_DEBUG', true);
     define('WP_DEBUG_LOG', true);
     define('WP_DEBUG_DISPLAY', false);
     ```

### Testing

**Manual Testing Checklist:**
- [ ] Plugin activates without errors
- [ ] Database tables created
- [ ] Admin menus appear
- [ ] Settings save correctly
- [ ] AWS connection test works
- [ ] Chat widget appears on frontend
- [ ] Messages send and receive
- [ ] Rate limiting enforces
- [ ] Logs are created
- [ ] Plugin deactivates cleanly

**Browser Testing:**
- Chrome (latest 2 versions)
- Firefox (latest 2 versions)
- Safari (latest 2 versions)
- Edge (latest 2 versions)

**Mobile Testing:**
- iOS Safari
- Chrome Mobile (Android)
- Various screen sizes

### Code Standards

- Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- Use WordPress functions for sanitization/escaping
- Comment all functions with PHPDoc
- Namespace everything to avoid conflicts

### Building for Production

```bash
# Install production dependencies only
composer install --no-dev --optimize-autoloader

# Remove development files
rm -rf .git .gitignore

# Create distributable ZIP
zip -r nerdsiq-ai-assistant.zip nerdsiq-ai-assistant/
```

## 🐛 Troubleshooting

### Common Issues

**1. Plugin Won't Activate**
- Check PHP version (must be 7.4+)
- Verify required extensions installed
- Check WordPress version (must be 5.8+)
- Review error logs

**2. AWS Connection Test Fails**
- Verify credentials are correct
- Check IAM permissions
- Ensure Q Business application exists
- Test from AWS CLI
- Check region is correct

**3. Chat Widget Not Appearing**
- Verify plugin is activated
- Check user has required role
- Confirm page is in allowed pages list
- Check browser console for JavaScript errors
- Clear cache (browser and server)

**4. Messages Not Sending**
- Check AWS connection
- Verify rate limits not exceeded
- Review error logs
- Test with debug mode enabled
- Check browser network tab

**5. Styles Look Broken**
- Clear all caches
- Check for theme conflicts
- Verify CSS file is loading
- Try increasing specificity in custom CSS

### Debug Mode

Enable debug mode to get detailed logs:

1. Go to NerdsIQ → Settings → Advanced
2. Check "Enable debug mode"
3. Save settings
4. Check logs: `wp-content/debug.log`

### Getting Help

**Documentation:**
- This README
- [WordPress Codex](https://codex.wordpress.org/)
- [AWS Q Business Docs](https://docs.aws.amazon.com/amazonq/)

**Support:**
- GitHub Issues: [Report a bug](https://github.com/nerdstogo/nerdsiq-ai-assistant/issues)
- Email: support@nerdstogo.com

## 📄 License

This plugin is licensed under the GNU General Public License v2.0 or later.

```
Copyright (C) 2024 NerdsToGo

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
```

## 🙏 Acknowledgments

- Built with [WordPress Plugin Boilerplate](https://github.com/DevinVinson/WordPress-Plugin-Boilerplate)
- Powered by [AWS Q Business](https://aws.amazon.com/q/)
- Icons from [Material Design Icons](https://materialdesignicons.com/)

---

**Made with ❤️ by NerdsToGo**

For more information, visit [https://nerdstogo.com](https://nerdstogo.com)
