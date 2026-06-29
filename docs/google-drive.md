# Google Drive API Setup Guide

This application uses the Google Drive API to automatically sync and fetch photos for client sessions. You need to configure a Google Cloud Service Account and generate a credentials JSON file.

## Step 1: Create a Google Cloud Project
1. Go to the [Google Cloud Console](https://console.cloud.google.com/).
2. Click on the project drop-down and select **New Project**.
3. Enter a project name and click **Create**.

## Step 2: Enable the Google Drive API
1. In the Cloud Console, go to **APIs & Services > Library**.
2. Search for **Google Drive API**.
3. Click on the result and click **Enable**.

## Step 3: Create a Service Account
1. Go to **APIs & Services > Credentials**.
2. Click **Create Credentials** and select **Service account**.
3. Provide a name and description, then click **Create and Continue**.
4. Grant the Service Account the role of **Project > Viewer** (or leave it empty if you only need access to specifically shared folders).
5. Click **Done**.

## Step 4: Generate Credentials JSON
1. On the Credentials page, click on the newly created Service Account email address.
2. Go to the **Keys** tab.
3. Click **Add Key > Create new key**.
4. Select **JSON** and click **Create**. The JSON file will be downloaded to your computer automatically.

## Step 5: Configure the Application
1. Rename the downloaded JSON file to `google-credentials.json`.
2. Move the file into the `storage/app/` directory of your Laravel project.
3. Open your `.env` file and ensure the following variables are set:
   ```env
   GOOGLE_APPLICATION_CREDENTIALS=storage/app/google-credentials.json
   GOOGLE_DRIVE_CACHE_MINUTES=10
   ```
*(Note: Adjust the cache minutes based on your preference for how frequently the app should check for new photos).*

## Step 6: Share Drive Folders with the Service Account
Because the Service Account acts as an independent user, it cannot automatically see your personal Google Drive files. 
1. Open your personal Google Drive in the browser.
2. Right-click the folder containing the photos for a session and select **Share**.
3. Add the email address of your Service Account (e.g., `my-service-account@my-project.iam.gserviceaccount.com`).
4. Set the permission to **Viewer**.
5. Once shared, you can copy the Folder ID from the URL (the long string of characters after `/folders/`) and use it in the application's Session creation form to sync photos.
