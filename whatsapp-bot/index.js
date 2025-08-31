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

    if (command === '/report') {
        const period = args[0] || 'daily';
        try {
            const response = await axios.get(`${LARAVEL_API_URL}/api/reports/${period}`);
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
    }
});

client.initialize();
