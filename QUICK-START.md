# NerdsIQ AI Assistant - Quick Start Guide

**For Non-Technical Users** 👋

This guide will help you install and configure the NerdsIQ AI Assistant plugin in simple, easy-to-follow steps. No technical knowledge required!

---

## 🎯 What This Plugin Does

This plugin adds an **AI chatbot** to your WordPress website. Team members can:
- Ask questions and get instant answers
- Search your company knowledge base
- Get help without contacting support

**Important**: Only logged-in WordPress users can access the chatbot (for security).

---

## 📋 What You Need Before Starting

Before you begin, make sure you have:

- ✅ **WordPress website** with admin access
- ✅ **AWS account** (Amazon Web Services)
- ✅ **30 minutes** of time
- ✅ **Pen and paper** (to write down credentials)

**Don't have AWS setup yet?** That's okay! We'll guide you through it.

---

## 🚀 Installation in 3 Simple Steps

### Step 1️⃣: Upload the Plugin (5 minutes)

1. **Download the plugin ZIP file**
   - You should have: `nerdsiq-ai-assistant-v1.0.0-production.zip`
   - Size: About 13 MB

2. **Log into your WordPress website**
   - Go to: `https://yourwebsite.com/wp-admin`
   - Enter your username and password

3. **Upload the plugin**
   - Look at the left sidebar
   - Click **"Plugins"** → **"Add New"**
   - Click the **"Upload Plugin"** button at the top
   - Click **"Choose File"**
   - Select the ZIP file you downloaded
   - Click **"Install Now"**
   - Wait for the green checkmark ✅

4. **Activate the plugin**
   - Click **"Activate Plugin"**
   - You should see "Plugin activated" message

**✅ Success!** You'll now see "NerdsIQ" in your left sidebar.

---

### Step 2️⃣: Get Your AWS Credentials (15 minutes)

You need **4 pieces of information** from Amazon AWS:

```
1. AWS Access Key ID
2. AWS Secret Access Key
3. AWS Region
4. Q Business Application ID
```

**How to get them:**

👉 **Follow our detailed guide**: [AWS-CONFIGURATION.md](AWS-CONFIGURATION.md)

This guide shows you:
- ✅ How to create an AWS account (if you don't have one)
- ✅ How to get each credential (with screenshots)
- ✅ Where to find each piece of information
- ✅ What to do if something goes wrong

**💡 TIP**: Open the AWS guide in a new tab and follow along step-by-step.

**Important Notes:**
- ⚠️ **Write these down!** You'll need them in Step 3
- ⚠️ **Keep them secret!** These are like passwords
- ⚠️ The "Secret Access Key" is shown only ONCE - save it immediately

---

### Step 3️⃣: Configure the Plugin (10 minutes)

Now let's add your AWS credentials to the plugin:

1. **Open Plugin Settings**
   - In WordPress admin, look at the left sidebar
   - Click **"NerdsIQ"**
   - Click **"Settings"**

2. **Enter Your AWS Information**

   You'll see 4 boxes to fill in:

   **Box 1: AWS Access Key ID**
   ```
   Looks like: AKIAIOSFODNN7EXAMPLE
   Paste yours here (starts with "AKIA")
   ```

   **Box 2: AWS Secret Access Key**
   ```
   Looks like: wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY
   Paste yours here (long random text)
   ⚠️ This will be encrypted when saved!
   ```

   **Box 3: AWS Region**
   ```
   Click the dropdown menu
   Select your region (example: US East - N. Virginia)
   Most common: us-east-1
   ```

   **Box 4: Q Business Application ID**
   ```
   Looks like: abc12345-6789-def0-1234-56789abcdef0
   Paste yours here (has dashes in it)
   ```

3. **Save Your Settings**
   - Scroll to the bottom
   - Click the blue **"Save Settings"** button
   - Wait for "Settings saved successfully" message ✅

4. **Test Your Connection**
   - Find the **"Test Connection"** button
   - Click it
   - Wait 5-10 seconds

   **What you should see:**
   - ✅ Green message: "Connection successful! Latency: XXXms"

   **If you see a red error message:**
   - Double-check you copied everything correctly
   - Make sure there are no extra spaces
   - See our [Troubleshooting Guide](#troubleshooting)

**✅ Success!** Your plugin is now connected to AWS!

---

## 🎨 Customize Your Chatbot (Optional)

Make the chatbot match your brand:

### Change Button Text

1. Click the **"Appearance"** tab
2. Find "Button Text"
3. Change from "Ask NerdsIQ" to whatever you want
4. Example: "Ask AI", "Get Help", "Questions?"
5. Click **"Save Settings"**

### Change Colors

1. Stay in the **"Appearance"** tab
2. Click the colored squares next to each color setting
3. Pick your brand colors
4. Click **"Save Settings"**

### Change Position

1. Stay in the **"Appearance"** tab
2. Find "Widget Position"
3. Choose "Bottom Right" or "Bottom Left"
4. Click **"Save Settings"**

---

## 🎉 You're Done! Test It Out

1. **Open your website** (not the admin area)
   - Go to: `https://yourwebsite.com`

2. **Log in as a regular user**
   - Use your WordPress account

3. **Look for the chatbot button**
   - Bottom right or bottom left corner
   - Should say "Ask NerdsIQ" (or what you changed it to)

4. **Click the button**
   - Chat window should open
   - You'll see a welcome message

5. **Ask a test question**
   - Type: "What services does NerdsToGo offer?"
   - Press Enter or click the send button
   - Wait for AI response (5-10 seconds)

**✅ If you get an answer:** Congratulations! Everything is working!

**❌ If nothing happens:** See [Troubleshooting](#troubleshooting) below

---

## 🔐 Set Who Can Use the Chatbot

By default, only Administrators and Editors can use the chatbot.

**To change this:**

1. Go to **NerdsIQ** → **Settings**
2. Click the **"Access Control"** tab
3. Check or uncheck user roles:
   - ✅ Administrator (recommended: keep checked)
   - ✅ Editor
   - ✅ Author
   - ✅ Contributor
   - ✅ Subscriber
4. Click **"Save Settings"**

**Who should have access?**
- Check = Can use the chatbot ✅
- Unchecked = Cannot use the chatbot ❌

---

## 🆘 Troubleshooting

### Problem: "Plugin won't activate"

**Check these:**
- ✅ WordPress version 5.8 or higher (Settings → General)
- ✅ PHP version 7.4 or higher (ask your hosting provider)
- ✅ You have administrator access

**Solution:** Contact your hosting provider to upgrade WordPress or PHP.

---

### Problem: "Test Connection fails"

**Error: "AWS credentials are not configured"**
- ✅ Make sure all 4 boxes are filled in
- ✅ Click "Save Settings" before testing

**Error: "Invalid credentials"**
- ✅ Copy credentials again from AWS
- ✅ Make sure no extra spaces at beginning or end
- ✅ Make sure you didn't mix up Access Key and Secret Key

**Error: "Application not found"**
- ✅ Check Application ID is correct
- ✅ Make sure Region matches where you created the app
- ✅ Verify app exists in AWS Q Business console

**Still stuck?**
- See detailed troubleshooting: [AWS-CONFIGURATION.md](AWS-CONFIGURATION.md#troubleshooting)
- Contact support: support@nerdstogo.com

---

### Problem: "Chatbot button doesn't appear"

**Check these:**
1. Are you logged in to WordPress?
   - ❌ Not logged in = no chatbot (security feature)
   - ✅ Logged in = chatbot should appear

2. Does your user role have access?
   - Go to NerdsIQ → Settings → Access Control
   - Make sure your role is checked

3. Are you on an allowed page?
   - Go to NerdsIQ → Settings → Access Control
   - Check "Display on Pages" setting
   - Make sure "All pages" is selected

4. Did you clear your browser cache?
   - Press Ctrl+Shift+Delete (PC) or Cmd+Shift+Delete (Mac)
   - Clear cache and reload

---

### Problem: "Can't send messages"

**Check these:**
- ✅ Connection test passes (green checkmark)
- ✅ You're on the correct AWS region
- ✅ No error messages in the chat window

**If you see "Rate limit exceeded":**
- You've sent too many messages
- Wait 1 hour and try again
- Admin can change limits: Settings → Behavior → Rate Limits

---

## 📞 Getting Help

### Documentation
- 📘 **This Quick Start** - You are here!
- 📗 **[AWS Setup Guide](AWS-CONFIGURATION.md)** - Detailed AWS instructions
- 📙 **[Installation Guide](INSTALL.md)** - Technical installation
- 📕 **[Full Documentation](README.md)** - Everything about the plugin

### Support
- **Email**: support@nerdstogo.com
- **GitHub**: [Report a bug](https://github.com/nerdstogo/nerdsiq-ai-assistant/issues)

### When Asking for Help

Please include:
1. **WordPress version** (Settings → General)
2. **PHP version** (ask your hosting provider)
3. **Error message** (copy the exact text)
4. **What you were doing** when the error happened

---

## ✅ Success Checklist

Use this to make sure everything is working:

- [ ] Plugin uploaded and activated
- [ ] AWS credentials entered
- [ ] Connection test passes (green checkmark)
- [ ] Chatbot button appears on website
- [ ] Can open chat window
- [ ] Can send a test message
- [ ] Receive AI response
- [ ] Message appears in Analytics (NerdsIQ → Analytics)

**All checked?** 🎉 You're all set!

---

## 🎓 Next Steps

Now that your chatbot is working:

1. **Customize the appearance**
   - Match your brand colors
   - Change welcome message
   - Add suggested questions

2. **Monitor usage**
   - NerdsIQ → Analytics
   - See who's using it
   - Check most asked questions

3. **Fine-tune settings**
   - Adjust rate limits if needed
   - Add or remove user roles
   - Customize behavior

---

## 💡 Tips for Success

### For Best Results:

1. **Write a good welcome message**
   - Make it friendly and helpful
   - Example: "Hi! I'm your AI assistant. Ask me anything about our services, policies, or procedures."

2. **Add suggested questions**
   - Settings → Appearance → Suggested Questions
   - Add 3-5 common questions
   - Users can click them to get started

3. **Monitor usage weekly**
   - Check Analytics dashboard
   - See what people are asking
   - Improve your knowledge base if needed

4. **Set reasonable rate limits**
   - Default: 50 messages/hour, 250/day
   - Adjust based on team size
   - Too low = frustrating for users
   - Too high = expensive AWS bills

5. **Keep credentials secure**
   - Never share your AWS keys
   - Rotate keys every 90 days
   - Use strong passwords for WordPress

---

## 🎨 Customization Ideas

### Welcome Message Examples:

**Friendly:**
```
👋 Hi! I'm NerdsIQ, your AI assistant.
Ask me anything about our services!
```

**Professional:**
```
Welcome to the NerdsToGo Knowledge Assistant.
How can I help you today?
```

**Helpful:**
```
Need help? I have answers about:
• Services & Pricing
• Technical Support
• Company Policies
Just ask!
```

### Suggested Question Examples:

```
• What services does NerdsToGo offer?
• How do I reset a customer's password?
• What are our service pricing tiers?
• How do I troubleshoot network issues?
• What's our refund policy?
```

---

## 📊 Understanding Analytics

Go to **NerdsIQ → Analytics** to see:

- **Total Conversations**: How many chats started
- **Total Messages**: How many questions asked
- **Unique Users**: How many different people used it
- **Average Response Time**: How fast AI responds
- **Most Active Users**: Who uses it most

Use this data to:
- See if team is using it
- Identify power users (give them admin access?)
- Check if response times are good (should be <5 seconds)
- Monitor for unusual activity

---

## 🔒 Security & Privacy

### What's Encrypted:
- ✅ AWS credentials (AES-256 encryption)
- ✅ All communication with AWS (HTTPS)

### What's Logged:
- ✅ Who used the chatbot
- ✅ What questions were asked
- ✅ When conversations happened

### What's NOT Logged:
- ❌ Passwords
- ❌ Credit card numbers
- ❌ Social security numbers

### Privacy Settings:
- Go to Settings → Advanced → Debug Mode
- Keep this OFF in production
- Only enable for troubleshooting

---

## 🎓 Glossary (Terms Explained)

**AWS** = Amazon Web Services (cloud computing platform)

**IAM** = Identity and Access Management (controls who can access what)

**Access Key** = Like a username for AWS (starts with "AKIA")

**Secret Key** = Like a password for AWS (long random text)

**Region** = Where AWS stores your data (example: US East)

**Q Business** = Amazon's AI chatbot service

**Application ID** = Unique identifier for your Q Business app

**Rate Limiting** = Maximum number of messages allowed per time period

**WordPress Admin** = Backend of your website (where you manage it)

**Frontend** = What visitors see on your website

**Plugin** = Add-on software for WordPress

---

**Need more help?** Check our other guides:
- [AWS Configuration Guide](AWS-CONFIGURATION.md) - Detailed AWS setup
- [Installation Guide](INSTALL.md) - Technical details
- [README](README.md) - Complete documentation

---

**Last Updated**: December 2024
**Plugin Version**: 1.0.0
**Difficulty Level**: ⭐ Beginner Friendly

**Made with ❤️ by NerdsToGo**
