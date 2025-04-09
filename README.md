# Phone Number Processor

A web application for processing and validating phone numbers. Built with Laravel and Vue.js.

## Features

- Upload CSV/XLS files containing phone numbers
- Paste phone numbers directly into the application
- Validate phone numbers against country-specific formats
- Split large sets of numbers into smaller chunks
- Download processed files
- Automatic file cleanup after 30 days

## Requirements

- PHP 8.1 or higher
- Node.js 16 or higher
- Composer
- npm or yarn

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/phone-number-processor.git
cd phone-number-processor
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install Node.js dependencies:
```bash
npm install
```

4. Create environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Create storage link:
```bash
php artisan storage:link
```

7. Run database migrations:
```bash
php artisan migrate
```

## Configuration

1. Update the `.env` file with your settings:
```
APP_NAME="Phone Number Processor"
APP_URL=http://localhost:8000
VITE_APP_URL=http://localhost:8000
VITE_API_URL=http://localhost:8000/api
```

2. Configure file upload settings in `config/upload.php`:
```php
return [
    'max_file_size' => 10240, // 10MB
    'allowed_extensions' => ['csv', 'xls', 'xlsx'],
    'storage_path' => 'uploads',
    'retention_days' => 30,
];
```

## Running the Application

1. Start the Laravel development server:
```bash
php artisan serve
```

2. Start the Vite development server:
```bash
npm run dev
```

3. Visit `http://localhost:8000` in your browser

## Usage

1. Upload a file:
   - Click "Choose File" to select a CSV or XLS file
   - Select the country code for the phone numbers
   - Set the desired chunk size
   - Click "Process Numbers"

2. Paste numbers:
   - Enter phone numbers in the text area (one per line or comma-separated)
   - Select the country code
   - Set the chunk size
   - Click "Process Numbers"

3. Download processed files:
   - View the list of processed files
   - Click "Download" to save a file

## Development

- Run tests:
```bash
php artisan test
```

- Build assets for production:
```bash
npm run build
```

- Format code:
```bash
npm run format
```

- Lint code:
```bash
npm run lint
```

## License

This project is licensed under the MIT License - see the LICENSE file for details. 
