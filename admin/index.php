<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="5;url=login.php">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #121212;
            margin: 0;
            color: white;
            font-family: Arial, sans-serif;
        }
        .loader {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        svg {
            width: 80px;
            height: 100px;
        }
        svg path,
        svg rect {
            fill: #FFFFFF;
        }
    </style>
</head>
<body>
    <section class="loader loader--style5" title="Loading">
        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
             xmlns:xlink="http://www.w3.org/1999/xlink"
             x="0px" y="0px" width="80px" height="100px"
             viewBox="0 0 30 30" xml:space="preserve">
            <rect x="2" y="0" width="6" height="15" fill="#ffffff">
                <animateTransform attributeType="xml"
                    attributeName="transform" type="translate"
                    values="0 0; 0 20; 0 0"
                    begin="0s" dur="0.6s" repeatCount="indefinite" />
            </rect>
            <rect x="12" y="0" width="6" height="15" fill="#ffffff">
                <animateTransform attributeType="xml"
                    attributeName="transform" type="translate"
                    values="0 0; 0 20; 0 0"
                    begin="0.2s" dur="0.6s" repeatCount="indefinite" />
            </rect>
            <rect x="22" y="0" width="6" height="15" fill="#ffffff">
                <animateTransform attributeType="xml"
                    attributeName="transform" type="translate"
                    values="0 0; 0 20; 0 0"
                    begin="0.4s" dur="0.6s" repeatCount="indefinite" />
            </rect>
        </svg>
    </section>
</body>
</html>
