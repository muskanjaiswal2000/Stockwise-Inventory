import './bootstrap';
import { Html5Qrcode } from 'html5-qrcode';

window.StockwiseHtml5Qrcode = Html5Qrcode;

window.downloadStockwiseQr = async function (imageUrl) {
    const response = await fetch(imageUrl, {credentials: 'same-origin'});
    if (!response.ok) throw new Error('Unable to download the QR code.');

    const blobUrl = URL.createObjectURL(await response.blob());
    const link = document.createElement('a');
    link.href = blobUrl;
    link.download = 'stockwise-product-catalog.png';
    link.click();
    URL.revokeObjectURL(blobUrl);
};

let stockwiseReader;

window.dropStockwiseQr = async function (event) {
    event.preventDefault();
    const message = document.getElementById('stockwise-scanner-message');
    const imageUrl = event.dataTransfer.getData('text/uri-list') || event.dataTransfer.getData('text/plain');
    const file = event.dataTransfer.files?.[0];

    try {
        const reader = new window.StockwiseHtml5Qrcode('stockwise-camera');
        let scanFile = file;
        if (!scanFile && imageUrl) {
            const response = await fetch(imageUrl);
            scanFile = new File([await response.blob()], 'stockwise-qr.png', {type: 'image/png'});
        }
        if (!scanFile) throw new Error('Drop a QR image file or the QR image itself.');
        const decodedText = await reader.scanFile(scanFile, true);
        window.location.href = decodedText;
    } catch (error) {
        if (message) message.textContent = 'That image could not be read. Drag the QR image into this box.';
    }
};

