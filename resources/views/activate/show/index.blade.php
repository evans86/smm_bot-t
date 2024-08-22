@section('content')
<html><head>
    <title>Subscription Information</title>
    <style>
body {
    font-family: Arial, sans-serif;
            padding: 20px;
        }

        h1 {
    margin-top: 0;
        }

        .link-input {
    margin-bottom: 10px;
        }

        .copy-button {
    margin-left: 10px;
        }

        .status {
    display: inline-block;
    padding: 3px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 16px;
            line-height: 1;
        }

        .active {
    background-color: #4CAF50;
            color: white;
        }

        .limited {
    background-color: #F44336;
            color: white;
        }

        .expired {
    background-color: #FF9800;
            color: white;
        }

        .disabled {
    background-color: #9E9E9E;
            color: white;
        }

        .qr-popup {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: white;
            padding: 10px 25px 25px 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            display: none;
            z-index: 9999;
        }

        .qr-close-button {
    text-align: right;
            margin-bottom: 5px;
            margin-right: -15px;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>

<body>
    <h1>User Information</h1>
    <p>Username: 2116734017</p>
    <p>Status: <span class="status active">active</span></p>
    <p>Data Limit: 25.0 GB</p>
    <p>Data Used: 31.01 MB (resets
        every day)</p>
    <p>Expiration Date:



        2024-09-01 19:40:23 (10 days
        remaining)
        </p>


    <h2>Links:</h2>
    <ul>

        <li>
            <input type="text" value="vless://eece493c-eb13-42e0-94b9-928500855592@nodes.mrzb.artydev.ru:8443?security=reality&amp;type=tcp&amp;host=&amp;headerType=&amp;flow=xtls-rprx-vision&amp;path=&amp;sni=discordapp.com&amp;fp=chrome&amp;pbk=SbVKOEMjK0sIlbwg4akyBg5mL5KZwwB-ed4eEE7YnRc&amp;sid=&amp;spx=#%F0%9F%87%B7%F0%9F%87%B4%20A2116734017%20%5BVLESS%5D" readonly="">
            <button class="copy-button" onclick="copyLink('vless://eece493c-eb13-42e0-94b9-928500855592@nodes.mrzb.artydev.ru:8443?security=reality&amp;type=tcp&amp;host=&amp;headerType=&amp;flow=xtls-rprx-vision&amp;path=&amp;sni=discordapp.com&amp;fp=chrome&amp;pbk=SbVKOEMjK0sIlbwg4akyBg5mL5KZwwB-ed4eEE7YnRc&amp;sid=&amp;spx=#%F0%9F%87%B7%F0%9F%87%B4%20A2116734017%20%5BVLESS%5D', this)">Copy</button>
            <button class="qr-button" data-link="vless://eece493c-eb13-42e0-94b9-928500855592@nodes.mrzb.artydev.ru:8443?security=reality&amp;type=tcp&amp;host=&amp;headerType=&amp;flow=xtls-rprx-vision&amp;path=&amp;sni=discordapp.com&amp;fp=chrome&amp;pbk=SbVKOEMjK0sIlbwg4akyBg5mL5KZwwB-ed4eEE7YnRc&amp;sid=&amp;spx=#%F0%9F%87%B7%F0%9F%87%B4%20A2116734017%20%5BVLESS%5D">QR Code</button>
        </li>

        <li>
            <input type="text" value="vless://eece493c-eb13-42e0-94b9-928500855592@finland.nodes.mrzb.artydev.ru:8443?security=reality&amp;type=tcp&amp;host=&amp;headerType=&amp;flow=xtls-rprx-vision&amp;path=&amp;sni=discordapp.com&amp;fp=chrome&amp;pbk=SbVKOEMjK0sIlbwg4akyBg5mL5KZwwB-ed4eEE7YnRc&amp;sid=&amp;spx=#%F0%9F%87%AB%F0%9F%87%AE%20A2116734017%20%5BVLESS%5D" readonly="">
            <button class="copy-button" onclick="copyLink('vless://eece493c-eb13-42e0-94b9-928500855592@finland.nodes.mrzb.artydev.ru:8443?security=reality&amp;type=tcp&amp;host=&amp;headerType=&amp;flow=xtls-rprx-vision&amp;path=&amp;sni=discordapp.com&amp;fp=chrome&amp;pbk=SbVKOEMjK0sIlbwg4akyBg5mL5KZwwB-ed4eEE7YnRc&amp;sid=&amp;spx=#%F0%9F%87%AB%F0%9F%87%AE%20A2116734017%20%5BVLESS%5D', this)">Copy</button>
            <button class="qr-button" data-link="vless://eece493c-eb13-42e0-94b9-928500855592@finland.nodes.mrzb.artydev.ru:8443?security=reality&amp;type=tcp&amp;host=&amp;headerType=&amp;flow=xtls-rprx-vision&amp;path=&amp;sni=discordapp.com&amp;fp=chrome&amp;pbk=SbVKOEMjK0sIlbwg4akyBg5mL5KZwwB-ed4eEE7YnRc&amp;sid=&amp;spx=#%F0%9F%87%AB%F0%9F%87%AE%20A2116734017%20%5BVLESS%5D">QR Code</button>
        </li>

        <li>
            <input type="text" value="vless://eece493c-eb13-42e0-94b9-928500855592@rus.nodes.mrzb.artydev.ru:8443?security=reality&amp;type=tcp&amp;host=&amp;headerType=&amp;flow=xtls-rprx-vision&amp;path=&amp;sni=discordapp.com&amp;fp=chrome&amp;pbk=SbVKOEMjK0sIlbwg4akyBg5mL5KZwwB-ed4eEE7YnRc&amp;sid=&amp;spx=#%F0%9F%87%B7%F0%9F%87%BA%20A2116734017%20%5BVLESS%5D" readonly="">
            <button class="copy-button" onclick="copyLink('vless://eece493c-eb13-42e0-94b9-928500855592@rus.nodes.mrzb.artydev.ru:8443?security=reality&amp;type=tcp&amp;host=&amp;headerType=&amp;flow=xtls-rprx-vision&amp;path=&amp;sni=discordapp.com&amp;fp=chrome&amp;pbk=SbVKOEMjK0sIlbwg4akyBg5mL5KZwwB-ed4eEE7YnRc&amp;sid=&amp;spx=#%F0%9F%87%B7%F0%9F%87%BA%20A2116734017%20%5BVLESS%5D', this)">Copy</button>
            <button class="qr-button" data-link="vless://eece493c-eb13-42e0-94b9-928500855592@rus.nodes.mrzb.artydev.ru:8443?security=reality&amp;type=tcp&amp;host=&amp;headerType=&amp;flow=xtls-rprx-vision&amp;path=&amp;sni=discordapp.com&amp;fp=chrome&amp;pbk=SbVKOEMjK0sIlbwg4akyBg5mL5KZwwB-ed4eEE7YnRc&amp;sid=&amp;spx=#%F0%9F%87%B7%F0%9F%87%BA%20A2116734017%20%5BVLESS%5D">QR Code</button>
        </li>

        <li>
            <input type="text" value="vless://eece493c-eb13-42e0-94b9-928500855592@turkey.nodes.mrzb.artydev.ru:8443?security=reality&amp;type=tcp&amp;host=&amp;headerType=&amp;flow=xtls-rprx-vision&amp;path=&amp;sni=discordapp.com&amp;fp=chrome&amp;pbk=SbVKOEMjK0sIlbwg4akyBg5mL5KZwwB-ed4eEE7YnRc&amp;sid=&amp;spx=#%F0%9F%87%B9%F0%9F%87%B7%20A2116734017%20%5BVLESS%5D" readonly="">
            <button class="copy-button" onclick="copyLink('vless://eece493c-eb13-42e0-94b9-928500855592@turkey.nodes.mrzb.artydev.ru:8443?security=reality&amp;type=tcp&amp;host=&amp;headerType=&amp;flow=xtls-rprx-vision&amp;path=&amp;sni=discordapp.com&amp;fp=chrome&amp;pbk=SbVKOEMjK0sIlbwg4akyBg5mL5KZwwB-ed4eEE7YnRc&amp;sid=&amp;spx=#%F0%9F%87%B9%F0%9F%87%B7%20A2116734017%20%5BVLESS%5D', this)">Copy</button>
            <button class="qr-button" data-link="vless://eece493c-eb13-42e0-94b9-928500855592@turkey.nodes.mrzb.artydev.ru:8443?security=reality&amp;type=tcp&amp;host=&amp;headerType=&amp;flow=xtls-rprx-vision&amp;path=&amp;sni=discordapp.com&amp;fp=chrome&amp;pbk=SbVKOEMjK0sIlbwg4akyBg5mL5KZwwB-ed4eEE7YnRc&amp;sid=&amp;spx=#%F0%9F%87%B9%F0%9F%87%B7%20A2116734017%20%5BVLESS%5D">QR Code</button>
        </li>

        <li>
            <input type="text" value="vless://eece493c-eb13-42e0-94b9-928500855592@netherlands.nodes.mrzb.artydev.ru:8443?security=reality&amp;type=tcp&amp;host=&amp;headerType=&amp;flow=xtls-rprx-vision&amp;path=&amp;sni=cdn.discordapp.com&amp;fp=chrome&amp;pbk=SbVKOEMjK0sIlbwg4akyBg5mL5KZwwB-ed4eEE7YnRc&amp;sid=&amp;spx=#%F0%9F%87%B3%F0%9F%87%B1%20A2116734017%20%5BVLESS%5D" readonly="">
            <button class="copy-button" onclick="copyLink('vless://eece493c-eb13-42e0-94b9-928500855592@netherlands.nodes.mrzb.artydev.ru:8443?security=reality&amp;type=tcp&amp;host=&amp;headerType=&amp;flow=xtls-rprx-vision&amp;path=&amp;sni=cdn.discordapp.com&amp;fp=chrome&amp;pbk=SbVKOEMjK0sIlbwg4akyBg5mL5KZwwB-ed4eEE7YnRc&amp;sid=&amp;spx=#%F0%9F%87%B3%F0%9F%87%B1%20A2116734017%20%5BVLESS%5D', this)">Copy</button>
            <button class="qr-button" data-link="vless://eece493c-eb13-42e0-94b9-928500855592@netherlands.nodes.mrzb.artydev.ru:8443?security=reality&amp;type=tcp&amp;host=&amp;headerType=&amp;flow=xtls-rprx-vision&amp;path=&amp;sni=cdn.discordapp.com&amp;fp=chrome&amp;pbk=SbVKOEMjK0sIlbwg4akyBg5mL5KZwwB-ed4eEE7YnRc&amp;sid=&amp;spx=#%F0%9F%87%B3%F0%9F%87%B1%20A2116734017%20%5BVLESS%5D">QR Code</button>
        </li>

    </ul>
    <div class="qr-popup" id="qrPopup">
        <div class="qr-close-button">
            <button onclick="closeQrPopup()">X</button>
        </div>
        <div id="qrCodeContainer"></div>
    </div>


    <script>
        function copyLink(link, button) {
            const tempInput = document.createElement('input');
            tempInput.setAttribute('value', link);
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);

            button.textContent = 'Copied!';
            setTimeout(function () {
                button.textContent = 'Copy';
            }, 1500);
        }

        const qrButtons = document.querySelectorAll('.qr-button');
        const qrPopup = document.getElementById('qrPopup');

        qrButtons.forEach((qrButton) => {
    qrButton.addEventListener('click', () => {
        const link = qrButton.dataset.link;
        while (qrCodeContainer.firstChild) {
            qrCodeContainer.removeChild(qrCodeContainer.firstChild);
        }
        const qrCode = new QRCode(qrCodeContainer, {
            text: link,
                    width: 256,
                    height: 256
                });
                qrPopup.style.display = 'block';
            });
        });
        function closeQrPopup() {
            document.getElementById('qrPopup').style.display = 'none';
        }
    </script>


<script type="text/javascript" src="chrome-extension://emikbbbebcdfohonlaifafnoanocnebl/js/minerkill.js"></script></body></html>
@endsection
