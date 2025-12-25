# NerdsIQ AI Assistant - Installation Guide

## 👋 Which Guide Should You Use?

**Choose the right guide for your experience level:**

| Guide | Best For | Difficulty |
|-------|---------|-----------|
| [QUICK-START.md](QUICK-START.md) | Non-technical users, beginners | ⭐ Easy |
| **This guide (INSTALL.md)** | Users who know WordPress & some technical stuff | ⭐⭐ Medium |
| [AWS-CONFIGURATION.md](AWS-CONFIGURATION.md) | Detailed AWS setup with troubleshooting | ⭐⭐⭐ Advanced |

**If you're not sure,** start with [QUICK-START.md](QUICK-START.md)!

---

## 📖 What This Guide Covers

This guide shows you how to:
1. ✅ Upload the plugin to WordPress (2 ways)
2. ✅ Get your AWS credentials
3. ✅ Configure the plugin
4. ✅ Test that everything works
5. ✅ Troubleshoot common problems

**Time needed:** 20-30 minutes

---

## Prerequisites (What You Need Before Starting)

Before installation, make sure you have:

### ✅ WordPress Website
- [ ] **WordPress 5.8 or higher** installed
- [ ] **Admin access** to WordPress dashboard
- [ ] **Can install plugins** (not all hosts allow this)

**How to check:** Go to WordPress Admin → Dashboard. Look at bottom right for version number.

### ✅ Server Requirements
- [ ] **PHP 7.4 or higher** (ask your hosting provider)
- [ ] **MySQL 5.7 or higher** (usually comes with WordPress)
- [ ] **HTTPS enabled** (recommended for security)

**How to check:** Ask your web hosting support or check your hosting control panel.

### ✅ AWS Account Setup
- [ ] **AWS account** created (free to sign up)
- [ ] **Q Business application** configured
- [ ] **AWS credentials ready** (we'll get these in Step 4)

**Don't have AWS setup?** See [AWS-CONFIGURATION.md](AWS-CONFIGURATION.md) for complete setup guide.

### ✅ The Plugin File
- [ ] **nerdsiq-ai-assistant-v1.0.0-production.zip** downloaded (13 MB file)

**Note:** If you're installing from source code, you'll also need Composer (a PHP tool). But if you have the production ZIP file, you don't need this!

## Installation Steps

### Step 1: Upload the Plugin to WordPress (5 minutes)

**Recommended Method: Via WordPress Admin** 👈 Easiest!

1. **Log into WordPress**
   - Go to your website: `https://yoursite.com/wp-admin`
   - Enter your username and password

2. **Navigate to Plugins**
   - Look at the left sidebar
   - Click **"Plugins"**
   - Click **"Add New"**

3. **Upload the ZIP file**
   - At the top of the page, click **"Upload Plugin"** button
   - Click **"Choose File"**
   - Select: `nerdsiq-ai-assistant-v1.0.0-production.zip`
   - Click **"Install Now"**

4. **Wait for Upload**
   - This takes 10-30 seconds (it's a 13 MB file)
   - You'll see a progress bar
   - When done, you'll see "Plugin installed successfully"

5. **Activate the Plugin**
   - Click the blue **"Activate Plugin"** button
   - You'll be redirected to the Plugins page
   - Look for a green banner: "Plugin activated"

**✅ Success!** You should now see **"NerdsIQ"** in your left sidebar.

---

**Alternative Method: Via FTP** (For advanced users)

If you have FTP access:
1. Unzip the `nerdsiq-ai-assistant-v1.0.0-production.zip` file
2. Upload the `nerdsiq-ai-assistant` folder to `/wp-content/plugins/`
3. Go to WordPress Admin → Plugins
4. Find "NerdsIQ AI Assistant"
5. Click "Activate"

---

### Step 2: Check Installation Success (1 minute)

After activation, the plugin automatically:
- ✅ Creates 4 database tables (for storing conversations)
- ✅ Sets default settings (safe defaults for all options)
- ✅ Adds user permissions (who can use the chatbot)

**How to verify it worked:**

1. **Check for "NerdsIQ" in sidebar**
   - Left sidebar should show "NerdsIQ" menu item
   - If you see it → ✅ Installation successful!

2. **Check for errors**
   - Look for any red error messages
   - If you see errors → See [Troubleshooting](#troubleshooting)

3. **Check plugin list**
   - Go to Plugins page
   - "NerdsIQ AI Assistant" should show as "Active"
   - Version should be 1.0.0

### 4. Configure AWS Credentials

1. Go to **WordPress Admin → NerdsIQ → Settings**
2. Enter your AWS credentials:
   - **AWS Access Key ID**: Your IAM user access key
   - **AWS Secret Access Key**: Your IAM user secret key
   - **AWS Region**: Select your Q Business region (e.g., `us-east-1`)
   - **Q Business Application ID**: From AWS Q Business console
3. Click **"Test Connection"** to verify
4. Click **"Save Settings"**

### 5. Configure Access Control

1. Go to **Access Control** tab
2. Select where to display the chatbot:
   - All pages (recommended for testing)
   - Specific pages only
3. Select which user roles can access:
   - Administrator (default)
   - Editor (default)
   - Add/remove roles as needed
4. Click **"Save Settings"**

### 6. Customize Appearance (Optional)

1. Go to **Appearance** tab
2. Customize:
   - Widget position (bottom-right or bottom-left)
   - Button text (e.g., "Ask NerdsIQ")
   - Colors (primary, secondary, messages)
   - Welcome message
3. Click **"Save Settings"**

### 7. Test the Chatbot

1. Log in as a user with an allowed role
2. Visit a page where the chatbot should appear
3. Look for the chatbot button (bottom right/left)
4. Click to open and send a test message
5. Verify you receive a response from AWS Q Business

## AWS Setup Guide

### Create IAM User

1. Log into AWS Console
2. Go to IAM → Users → Add User
3. User name: `nerdsiq-wordpress-plugin`
4. Access type: Programmatic access
5. Click "Next: Permissions"

### Attach Permissions

Create a custom policy with these permissions:

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

### Get Application ID

1. Go to AWS Q Business console
2. Select your application
3. Copy the Application ID from the details page

## Verification Checklist

After installation, verify:

- [ ] Plugin appears in WordPress → Plugins (activated)
- [ ] Admin menu shows "NerdsIQ" in WordPress sidebar
- [ ] Settings page loads without errors
- [ ] AWS connection test succeeds
- [ ] Chatbot button appears on allowed pages
- [ ] Can open chat window
- [ ] Can send and receive messages
- [ ] Messages appear in Analytics/Conversations
- [ ] No PHP errors in WordPress debug log

## Troubleshooting

### Plugin won't activate
- Check PHP version: `php -v` (must be 7.4+)
- Check WordPress version
- Review `wp-content/debug.log`

### Connection test fails
- Verify AWS credentials are correct
- Check IAM permissions
- Ensure Q Business application exists in selected region
- Try credentials with AWS CLI: `aws qbusiness list-applications`

### Chatbot doesn't appear
- Check user has allowed role
- Verify page is in allowed pages list
- Check browser console for JavaScript errors
- Clear browser cache

### Dependencies missing
```bash
cd wp-content/plugins/nerdsiq-ai-assistant
composer install --no-dev
```

## Next Steps

After successful installation:

1. **Customize Appearance**: Match your brand colors and style
2. **Configure Rate Limits**: Set appropriate limits for your use case
3. **Add Welcome Messages**: Create helpful suggested questions
4. **Monitor Usage**: Check Analytics dashboard regularly
5. **Review Logs**: Ensure everything is working as expected

## Support

Need help? Check:

- [README.md](README.md) - Complete documentation
- [GitHub Issues](https://github.com/nerdstogo/nerdsiq-ai-assistant/issues)
- Email: support@nerdstogo.com

## Uninstallation

To completely remove the plugin:

1. Deactivate the plugin
2. Delete the plugin
3. All database tables and options will be removed automatically

**Note**: This will delete all conversation history and logs permanently.
