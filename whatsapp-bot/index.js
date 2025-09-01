// whatsapp-bot/index.js

const { Client, MessageMedia } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const axios = require('axios');
const FormData = require('form-data');

// Use an environment variable for the API URL, with a default for local development
const LARAVEL_API_URL = process.env.LARAVEL_API_URL || 'http://localhost:8000';

const client = new Client();

client.on('qr', (qr) => {
    qrcode.generate(qr, { small: true });
});

client.on('ready', () => {
    console.log('Client is ready!');
});

client.on('message', async (message) => {
    const command = message.body.split(' ')[0];
    const args = message.body.split(' ').slice(1);

    if (command === '/register') {
        const email = args[0];
        if (!email) {
            message.reply('Please provide your email address. Usage: /register <your_email>');
            return;
        }

        try {
            await axios.post(`${LARAVEL_API_URL}/api/register`, {
                email: email,
                whatsapp_number: message.from,
            });
            message.reply('You have been registered successfully!');
        } catch (error) {
            console.error('Error registering user:', error.message);
            if (error.response && error.response.status === 404) {
                message.reply('Sorry, I could not find a user with that email address.');
            } else {
                message.reply('Sorry, I could not register you at this time.');
            }
        }
        return;
    }

    if (command === '/report') {
        const period = args[0] || 'daily';
        try {
            const response = await axios.get(`${LARAVEL_API_URL}/api/reports/${period}`, {
                params: {
                    whatsapp_number: message.from,
                }
            });
            message.reply(response.data.report);
        } catch (error) {
            console.error('Error fetching report:', error.message);
            message.reply('Sorry, I could not generate the report.');
        }
    }

    if (message.hasMedia) {
        const media = await message.downloadMedia();
        const formData = new FormData();
        formData.append('file', Buffer.from(media.data, 'base64'), media.filename || 'upload.dat');
        formData.append('whatsapp_number', message.from);

        try {
            await axios.post(`${LARAVEL_API_URL}/api/document`, formData, {
                headers: {
                    ...formData.getHeaders()
                }
            });
            message.reply('I have received your document. I will process it shortly.');
        } catch (error) {
            console.error('Error processing document:', error.message);
            message.reply('Sorry, I could not process your document.');
        }
        return;
    }

    // Default help message
    const helpMessage = `
Hello! I'm your friendly invoice bot. Here's what I can do:

*Commands:*
- \`/register <your_email>\`: Register your WhatsApp number with your account.
- \`/report daily\`: Get a daily report of your invoices.
- \`/report weekly\`: Get a weekly report of your invoices.
- \`/report monthly\`: Get a monthly report of your invoices.

*To upload an invoice or receipt, simply send it to me as an image or PDF document.*
    `;
    message.reply(helpMessage.trim());
});

client.initialize();
