<?php
// Embed Power BI iframe with expanded width and centered alignment
$iframe_code = "
<div style='display: flex; justify-content: center;'>
    <iframe title='DASHBOARD - MTD | Summary' 
            width='1100' 
            height='700' 
            src='https://app.powerbi.com/reportEmbed?reportId=ec82f7e9-7a22-4cf0-baa2-40f9ab7677a1&autoAuth=true&ctid=e2fcf853-8bfc-47b9-812f-359fb0a13c63' 
            frameborder='0' 
            allowFullScreen='true'>
    </iframe>
</div>
";

// Output the iframe code
echo $iframe_code;
