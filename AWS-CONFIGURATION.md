# AWS Configuration Guide for NerdsIQ AI Assistant

Complete step-by-step guide to configure AWS credentials and Q Business for the NerdsIQ AI Assistant WordPress plugin.

---

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Step 1: Create IAM User](#step-1-create-iam-user)
3. [Step 2: Create IAM Policy](#step-2-create-iam-policy)
4. [Step 3: Attach Policy to User](#step-3-attach-policy-to-user)
5. [Step 4: Generate Access Keys](#step-4-generate-access-keys)
6. [Step 5: Get Q Business Application ID](#step-5-get-q-business-application-id)
7. [Step 6: Configure Plugin](#step-6-configure-plugin-in-wordpress)
8. [Step 7: Test Connection](#step-7-test-connection)
9. [Troubleshooting](#troubleshooting)
10. [Security Best Practices](#security-best-practices)

---

## Prerequisites

Before you begin, ensure you have:

- ✅ AWS Account with admin access
- ✅ AWS Q Business application already created
- ✅ S3 bucket with your knowledge base data
- ✅ WordPress site with NerdsIQ plugin installed
- ✅ WordPress admin access

---

## Step 1: Create IAM User

### 1.1 Navigate to IAM Console

1. Log into **AWS Management Console**: https://console.aws.amazon.com/
2. Search for **IAM** in the top search bar
3. Click on **IAM** service

### 1.2 Create New User

1. In the left sidebar, click **Users**
2. Click **Add users** (or **Create user** in newer AWS console)
3. Enter user details:
   - **User name**: `nerdsiq-wordpress-plugin` (recommended)
   - **Access type**: Select **Programmatic access** only
   - ⚠️ **Important**: Do NOT select "AWS Management Console access"
4. Click **Next: Permissions**

---

## Step 2: Create IAM Policy

### 2.1 Create Custom Policy

1. On the permissions page, click **Attach existing policies directly**
2. Click **Create policy** (opens new tab)
3. Click the **JSON** tab

### 2.2 Copy This Policy

Replace the default JSON with this policy:

```json
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Sid": "QBusinessChatAccess",
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
      "Sid": "S3KnowledgeBaseAccess",
      "Effect": "Allow",
      "Action": [
        "s3:GetObject",
        "s3:ListBucket"
      ],
      "Resource": [
        "arn:aws:s3:::YOUR-BUCKET-NAME",
        "arn:aws:s3:::YOUR-BUCKET-NAME/*"
      ]
    }
  ]
}
```

**⚠️ IMPORTANT**: Replace `YOUR-BUCKET-NAME` with your actual S3 bucket name!

### 2.3 Review and Create Policy

1. Click **Next: Tags** (optional, can skip)
2. Click **Next: Review**
3. Enter policy details:
   - **Name**: `NerdsIQWordPressPluginPolicy`
   - **Description**: "Permissions for NerdsIQ WordPress plugin to access Q Business"
4. Click **Create policy**
5. Close the tab and return to the user creation tab

---

## Step 3: Attach Policy to User

### 3.1 Attach the Policy

1. Click the **Refresh** button to see your new policy
2. In the search box, type: `NerdsIQWordPressPluginPolicy`
3. Check the box next to your policy
4. Click **Next: Tags**

### 3.2 Add Tags (Optional)

1. Optionally add tags like:
   - Key: `Application`, Value: `NerdsIQ`
   - Key: `Environment`, Value: `Production`
2. Click **Next: Review**

### 3.3 Review and Create

1. Review the user details
2. Ensure **Programmatic access** is enabled
3. Ensure your policy is attached
4. Click **Create user**

---

## Step 4: Generate Access Keys

### 4.1 Save Credentials Immediately

⚠️ **CRITICAL**: This is your ONLY chance to see the secret access key!

After creating the user, you'll see:

```
Access key ID: AKIAIOSFODNN7EXAMPLE
Secret access key: wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY
```

### 4.2 Download or Copy Credentials

**Option A: Download CSV**
1. Click **Download .csv**
2. Save the file in a secure location
3. ⚠️ Never commit this file to version control!

**Option B: Copy Manually**
1. Copy **Access key ID** to a secure note
2. Click **Show** next to Secret access key
3. Copy **Secret access key** to a secure note

### 4.3 Important Security Notes

- ✅ Store credentials in a password manager
- ✅ Never share these credentials
- ✅ Never commit to Git repositories
- ✅ Rotate keys every 90 days
- ❌ Never email or message these keys
- ❌ Never store in plain text files

---

## Step 5: Get Q Business Application ID

### 5.1 Navigate to Q Business Console

1. In AWS Console, search for **Q Business**
2. Click on **Amazon Q Business** service
3. Make sure you're in the **correct region** (check top-right dropdown)

### 5.2 Find Your Application

1. You should see a list of your Q Business applications
2. Click on your application name (e.g., "NerdsToGo Knowledge Base")
3. You'll see the application details page

### 5.3 Copy Application ID

1. Look for **Application ID** field
2. It looks like: `abc12345-6789-def0-1234-56789abcdef0`
3. Click the **Copy** icon or manually select and copy
4. Save this ID - you'll need it for WordPress configuration

### 5.4 Note Your Region

While you're here, note the AWS region you're using:
- Check the top-right corner of AWS Console
- Common regions:
  - `us-east-1` (US East - N. Virginia)
  - `us-west-2` (US West - Oregon)
  - `eu-west-1` (Europe - Ireland)
  - `ap-southeast-1` (Asia Pacific - Singapore)

---

## Step 6: Configure Plugin in WordPress

Now that you have all the required information, configure the plugin:

### 6.1 Navigate to Plugin Settings

1. Log into **WordPress Admin**
2. In the left sidebar, click **NerdsIQ**
3. Click **Settings** (or it may open automatically)
4. You should see the **General Settings** tab

### 6.2 Enter AWS Credentials

Fill in these fields:

#### AWS Access Key ID
```
Paste your Access key ID here
Example: AKIAIOSFODNN7EXAMPLE
```

#### AWS Secret Access Key
```
Paste your Secret access key here
Example: wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY
```
⚠️ **Security Note**: This will be encrypted when saved!

#### AWS Region
```
Select from dropdown: your Q Business region
Example: us-east-1
```

#### Q Business Application ID
```
Paste your Application ID here
Example: abc12345-6789-def0-1234-56789abcdef0
```

### 6.3 Save Settings

1. Double-check all fields are filled correctly
2. Click **"Save Settings"** button at the bottom
3. Wait for the success message: "Settings saved successfully"

---

## Step 7: Test Connection

### 7.1 Run Connection Test

1. After saving, look for the **"Test Connection"** button
2. Click **"Test Connection"**
3. Wait a few seconds for the test to complete

### 7.2 Interpret Results

**✅ Success Response:**
```
Connection successful! Latency: 250ms
```
- Your credentials are correct
- Q Business application is accessible
- Plugin is ready to use

**❌ Error Responses:**

**Error: AWS credentials are not configured**
- Solution: Fill in all four fields and save

**Error: Invalid credentials**
- Solution: Double-check Access Key ID and Secret Key
- Make sure there are no extra spaces
- Try regenerating the access keys

**Error: Application not found**
- Solution: Verify Application ID is correct
- Check you're in the correct AWS region
- Ensure Q Business application exists

**Error: Access Denied**
- Solution: Check IAM policy is attached to user
- Verify policy has correct permissions
- Make sure policy references correct S3 bucket

**Error: Connection timeout**
- Solution: Check your server's internet connection
- Verify firewall allows outbound HTTPS (port 443)
- Check AWS service status

---

## Troubleshooting

### Problem: Connection Test Fails

**Check 1: Verify Credentials**
```bash
# Test credentials with AWS CLI
aws sts get-caller-identity \
  --aws-access-key-id YOUR_ACCESS_KEY \
  --aws-secret-access-key YOUR_SECRET_KEY
```

Expected output:
```json
{
    "UserId": "AIDAI...",
    "Account": "123456789012",
    "Arn": "arn:aws:iam::123456789012:user/nerdsiq-wordpress-plugin"
}
```

**Check 2: Test Q Business Access**
```bash
# List Q Business applications
aws qbusiness list-applications \
  --region us-east-1 \
  --aws-access-key-id YOUR_ACCESS_KEY \
  --aws-secret-access-key YOUR_SECRET_KEY
```

**Check 3: Verify IAM Permissions**
1. Go to IAM → Users → nerdsiq-wordpress-plugin
2. Click **Permissions** tab
3. Verify `NerdsIQWordPressPluginPolicy` is attached
4. Click on the policy name
5. Click **{} JSON** tab
6. Verify all required actions are present

**Check 4: Enable Debug Mode**
1. In WordPress, go to NerdsIQ → Settings → Advanced
2. Check "Enable debug mode"
3. Save settings
4. Try connection test again
5. Check `wp-content/debug.log` for detailed error messages

### Problem: Credentials Keep Getting Lost

This usually means encryption is failing:

1. Check WordPress `wp-config.php` has these constants defined:
   ```php
   define('AUTH_KEY', 'unique-key-here');
   define('SECURE_AUTH_KEY', 'unique-key-here');
   ```
2. These are required for credential encryption
3. Generate new keys at: https://api.wordpress.org/secret-key/1.1/salt/

### Problem: Wrong Region Selected

If you selected the wrong region:

1. Go back to AWS Console
2. Check which region your Q Business app is in (top-right)
3. In WordPress, change the region dropdown
4. Save settings
5. Test connection again

---

## Security Best Practices

### ✅ DO:

1. **Use Dedicated IAM User**
   - Create a user specifically for this plugin
   - Don't reuse credentials from other applications

2. **Principle of Least Privilege**
   - Only grant permissions the plugin needs
   - Don't give broader permissions "just in case"

3. **Rotate Credentials Regularly**
   - Rotate access keys every 90 days
   - Set a calendar reminder
   - Keep old keys active briefly during rotation

4. **Monitor Usage**
   - Enable AWS CloudTrail
   - Review IAM Access Advisor regularly
   - Check for unusual API calls

5. **Use MFA on AWS Account**
   - Enable MFA on your AWS root account
   - Enable MFA for all IAM admin users
   - This protects against credential theft

6. **Audit Access Logs**
   - Review WordPress → NerdsIQ → Usage Logs
   - Monitor for unusual activity
   - Set up alerts for errors

### ❌ DON'T:

1. **Never Use Root Credentials**
   - Don't use AWS root account access keys
   - Always create IAM users

2. **Never Hardcode Credentials**
   - Don't put credentials in theme files
   - Don't store in custom code
   - Only use the plugin settings page

3. **Never Share Credentials**
   - Each WordPress site should have its own IAM user
   - Don't reuse credentials across sites

4. **Never Commit to Git**
   - Check `.gitignore` excludes credential files
   - Use environment variables for staging/dev

---

## Quick Reference Card

### Required Information Checklist

```
☐ AWS Access Key ID: AKIA________________
☐ AWS Secret Access Key: ____________________
☐ AWS Region: ___________
☐ Q Business Application ID: ________-____-____-____-____________
```

### AWS Console URLs

- **IAM Console**: https://console.aws.amazon.com/iam/
- **Q Business Console**: https://console.aws.amazon.com/amazonq/
- **S3 Console**: https://s3.console.aws.amazon.com/

### WordPress Plugin URLs

- **Settings**: `/wp-admin/admin.php?page=nerdsiq-settings`
- **Analytics**: `/wp-admin/admin.php?page=nerdsiq-analytics`
- **Logs**: `/wp-admin/admin.php?page=nerdsiq-usage-logs`

---

## Getting Help

If you're still having issues:

1. **Check Documentation**
   - [README.md](README.md) - Complete plugin documentation
   - [INSTALL.md](INSTALL.md) - Installation guide

2. **Enable Debug Mode**
   - WordPress → NerdsIQ → Settings → Advanced
   - Enable "Debug Mode"
   - Check `wp-content/debug.log`

3. **Contact Support**
   - Email: support@nerdstogo.com
   - GitHub Issues: https://github.com/nerdstogo/nerdsiq-ai-assistant/issues
   - Include: WordPress version, PHP version, error messages

---

## Appendix: IAM Policy Variations

### Restrictive Policy (Single Application)

Limits access to one specific Q Business application:

```json
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Action": [
        "qbusiness:ChatSync",
        "qbusiness:ListMessages",
        "qbusiness:GetConversation"
      ],
      "Resource": "arn:aws:qbusiness:us-east-1:123456789012:application/abc12345-6789-def0-1234-56789abcdef0"
    },
    {
      "Effect": "Allow",
      "Action": [
        "s3:GetObject"
      ],
      "Resource": "arn:aws:s3:::your-bucket-name/*"
    }
  ]
}
```

### Multiple S3 Buckets

If your knowledge base uses multiple buckets:

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
        "arn:aws:s3:::knowledge-base-bucket-1",
        "arn:aws:s3:::knowledge-base-bucket-1/*",
        "arn:aws:s3:::knowledge-base-bucket-2",
        "arn:aws:s3:::knowledge-base-bucket-2/*"
      ]
    }
  ]
}
```

---

**Last Updated**: December 2024
**Plugin Version**: 1.0.0
**AWS SDK Version**: 3.369.2
