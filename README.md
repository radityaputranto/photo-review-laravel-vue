# Photo Reviewer App

A comprehensive photo review and selection platform built with Laravel 11, Vue 3, Inertia.js, and Tailwind CSS. This application allows photographers to share photo sessions with customers via Google Drive integration, and lets customers select and review their preferred photos.

## Features
- **Multi-Role Authentication**: Secure access for Super Admins, Admins, Photographers, and Customers.
- **Google Drive Integration**: Sync photos directly from Google Drive folders.
- **Card-based UI**: Modern, clean interface inspired by Google Stitch aesthetics.
- **Photo Selection**: Customers can easily browse and select their favorite photos.
- **Document Management**: Manage invoices and other documents securely.
- **Real-time Search**: Debounced, server-side search functionality for all listings.

## Documentation
Please refer to the `docs/` folder for detailed guides on how to install and configure the application.

- [Installation & Setup Guide](docs/installation.md)
- [Google Drive API Setup](docs/google-drive.md)

## Requirements
- PHP 8.2+
- Composer
- Node.js & NPM
- SQLite (Default) or MySQL/PostgreSQL

## Quick Start
1. Clone the repository.
2. Follow the [Installation Guide](docs/installation.md).

## License
The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
