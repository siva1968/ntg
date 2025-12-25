# NerdsIQ AI Assistant - Installation Guide

## Quick Start

Follow these steps to install and configure the NerdsIQ AI Assistant plugin.

## Prerequisites

Before installation, ensure you have:

- [ ] WordPress 5.8 or higher
- [ ] PHP 7.4 or higher
- [ ] MySQL 5.7 or higher
- [ ] AWS account with Q Business configured
- [ ] AWS IAM credentials
- [ ] Composer installed (for dependency management)

## Installation Steps

### 1. Prepare the Plugin

```bash
# Clone the repository
git clone https://github.com/nerdstogo/nerdsiq-ai-assistant.git
cd nerdsiq-ai-assistant

# Install PHP dependencies
composer install --no-dev --optimize-autoloader
```

### 2. Upload to WordPress

**Option A: Via WordPress Admin**
1. Compress the plugin folder to a ZIP file
2. Go to WordPress Admin → Plugins → Add New
3. Click "Upload Plugin"
4. Select the ZIP file and click "Install Now"
5. Click "Activate Plugin"

**Option B: Via FTP/SSH**
1. Upload the `nerdsiq-ai-assistant` folder to `wp-content/plugins/`
2. Go to WordPress Admin → Plugins
3. Find "NerdsIQ AI Assistant" and click "Activate"

### 3. Initial Configuration

After activation, the plugin will automatically:
- Create required database tables
- Set default options
- Add user capabilities
- Display a welcome message

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
