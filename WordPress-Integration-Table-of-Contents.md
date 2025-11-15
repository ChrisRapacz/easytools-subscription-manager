# How to Connect WordPress with Easytools – Table of Contents

## Introduction
- What you'll learn in this tutorial
- Two integration methods explained:
  - Method 1: Webhooks with Easytools Subscription Manager plugin
  - Method 2: Native Easytools Automations

---

## Part 1: Understanding Webhooks in Easytools

### 1.1 What are webhooks?
- Definition and how they work
- Real-time communication between Easytools and WordPress
- Why webhooks are important for subscription management

### 1.2 Easytools webhook events
- Explaining vailable events with a quick look into the JSON payload structure

### 1.3 Webhook security with HMAC
- What is webhook signing
- Why you need to verify webhooks
- Generating your Webhook Signing Key in Easytools
- Important: Saving the key securely (you won't see it again!)

---

## Part 2: Setting Up WordPress

### 2.1 Installing Easytools Subscription Manager plugin
- Downloading the plugin
- Installing via WordPress Admin Panel
- Activating the plugin
- First look at the plugin interface

### 2.2 Understanding WordPress user roles
- Default WordPress roles (Subscriber, Contributor, Author, Editor, Administrator)
- Which role to assign for paid subscribers

---

## Part 3: Configuring Easytools Subscription Manager Plugin

### 3.1 Accessing plugin settings
- Where to find the plugin in WordPress admin
- Overview of all plugin tabs

### 3.2 Basic Settings configuration
- Setting your Webhook Signing Key (from Easytools)
- Configuring Checkout URL (your Easytools store URL)
- Protecting entire site vs. specific pages

---

## Part 4: Setting Up Webhooks in Easytools

### 4.1 Configuring Webhook URL
- What is your webhook URL format

---

## Part 5: Testing Your Webhook Integration

### 5.1 Using the built-in Webhook Tester
- Accessing "Webhook Testing" tab in plugin
- Understanding example payloads
- Selecting a test event (e.g., Product Assigned)
- Editing payload data (optional)
- Sending test webhook
- Reading the response

### 5.2 Checking Webhook Logs
- Accessing "Webhook Logs" tab
- Understanding log entries:
  - Timestamp
  - Event type
  - Status (success/failure)
  - Full payload
  - Response
- Inspecting individual webhook details
- Using logs for debugging

### 5.3 HMAC Calculator
- What is the HMAC Calculator for
- When to use it (testing with Postman, external tools)
- Step-by-step:
  - Pasting your payload
  - Entering signing key
  - Generating signature
  - Copying signature for use in headers

### 5.4 Testing with Postman (Advanced)
- Setting up Postman request
- Method: POST
- URL: Your webhook endpoint
- Headers:
  - `Content-Type: application/json`
  - `x-webhook-signature: [generated signature]`
- Body: Raw JSON payload
- Sending and verifying response

---

## Part 6: Setting Up Native Easytools Automations

### 6.0 Generating Application Password in WordPress
- Why application passwords are needed
- Step-by-step: How to generate application password
- Where to find it in WordPress user profile
- Security best practices
- How to revoke access if needed

### 6.1 When to use Automations vs. Webhooks
- Can you use both? Yes! How they complement each other

### 6.2 Adding WordPress application in Easytools
- Going to Automations → Applications
- Clicking "+ New application"
- Selecting WordPress from the list
- Naming your integration

### 6.3 Connecting WordPress to Easytools
- Entering your WordPress site URL
- Using application username (your WordPress admin username)
- Pasting application password (generated in Part 6.0)
- Clicking "Check connection"
- Verifying successful connection
- Saving the application

### 6.4 Creating automation scenarios
- Going to Scenarios tab
- Clicking "+ New scenario"
- Selecting trigger event (e.g., "Order Completed")
- Choosing your WordPress application
- Selecting action: "Create User"
- Configuring user role (Subscriber, Author, etc.)
- Assigning to products/variants

### 6.5 Scenario execution and monitoring
- Where to find the Executions tab
- Reading execution logs
- Understanding success vs. failure status
- Retrying failed executions
- Canceling pending executions

---

## Part 7: Testing the Complete Flow

### 7.1 Creating a test subscriber
- Setting up a test product in Easytools
- Creating a test user account in WordPress
- Ensuring email addresses match

### 7.2 Protecting content in WordPress
- Creating a test page with premium content
- Using plugin to restrict access to this page (either the Easytools Subscription Manager or a different one if the first one is not installed - I will focus on the one called Simple Restrict).

### 7.3 Testing the redirect flow
- Logging out of WordPress (or using incognito)
- Trying to access protected page
- Verifying redirect to Easytools checkout
- What the user sees at each step
- Setting up "Bouncer" page to direct subscribers who are not logged in and the new users to the checkout

### 7.4 Completing a test purchase
- Filling out checkout form
- Completing payment

### 7.5 Verifying webhook delivery
- Expected timeline: 5-10 seconds
- Checking Webhook Logs in plugin
- Verifying user was updated in WordPress database
- Checking user's subscription status

### 7.6 Testing access unlock
- Returning to WordPress site
- Logging in (if needed)
- Accessing previously protected page
- Verifying content is now visible

### 7.7 Testing subscription expiration
- Manually triggering expiration webhook (test mode)
- Verifying access is revoked
- Testing redirect back to checkout