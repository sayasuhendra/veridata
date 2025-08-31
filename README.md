# Invoice and Receipt Processing Application

This is a Laravel application that automatically creates spending reports for clients by uploading invoices or receipts.

## Features

- **Filament Admin Panel**: A beautiful and extensible admin panel for managing invoices, receipts, and users.
- **AI-Powered Data Extraction**: Uses OpenAI's GPT-4o to automatically extract data from uploaded documents.
- **Asynchronous Job Processing**: Handles document processing in the background for a non-blocking user experience.

## WhatsApp Bot Integration

This application includes a WhatsApp bot that allows users to interact with the application through WhatsApp.

### Features

- **Upload Invoices and Receipts**: Users can send images or PDFs of their invoices and receipts directly to the bot.
- **Generate Reports**: Users can request daily, weekly, or monthly reports by sending a command to the bot.

### How to Use

1. **Start the application**:
   ```bash
   php artisan serve
   ```
2. **Start the WhatsApp bot**:
   ```bash
   npm run start:bot
   ```
3. **Scan the QR Code**: Open WhatsApp on your phone and scan the QR code that appears in the terminal to connect your phone to the bot.
4. **Send Documents**: Send an image or PDF of an invoice or receipt to the bot. The bot will process the document and save the extracted data.
5. **Request Reports**: Send a command to the bot to get a report:
   - `/report daily`
   - `/report weekly`
   - `/report monthly`

### API Endpoints

The WhatsApp bot communicates with the Laravel application through the following API endpoints:

- `POST /api/document`: Upload a new document for processing.
- `GET /api/reports/{period}`: Get a report for the specified period (`daily`, `weekly`, `monthly`).
