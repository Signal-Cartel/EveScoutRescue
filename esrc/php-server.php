<?php
if (session_id() == "") session_start();

// Handle session destruction
if (isset($_POST['destroy_session'])) {
    session_unset();
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

print "<pre>";
echo "Session ID: " . session_id() . "\n";
print_r($_SESSION);
print "</pre>";


?>

<!-- Session Destroy Button -->
<form method="post" style="display:inline;">
    <button type="submit" name="destroy_session">Destroy Session</button>
</form>

<!-- Delete localStorage Button -->
<button onclick="deleteAllisonSettings()">Delete 'allisonSettings' (localStorage)</button>

<!-- Refresh localStorage display -->
<button onclick="displayLocalStorage()">Refresh LocalStorage Display</button>

<!-- LocalStorage Display Section -->
<div id="localStorageDisplay" style="margin-top: 10px; background-color: #f4f4f4; padding: 10px; border-radius: 5px;">
    <strong>LocalStorage Contents:</strong>
    <pre id="localStorageContents">[Loading...]</pre>
</div>

<script>
function deleteAllisonSettings() {
    localStorage.removeItem('allisonSettings');
    alert("'allisonSettings' deleted from localStorage.");
    displayLocalStorage();
}

function displayLocalStorage() {
    const displayArea = document.getElementById('localStorageContents');
    let output = '';

    for (let i = 0; i < localStorage.length; i++) {
        const key = localStorage.key(i);
        const rawValue = localStorage.getItem(key);

        let displayValue;

        try {
            const parsedValue = JSON.parse(rawValue);
            displayValue = JSON.stringify(parsedValue, null, 4); // Pretty print JSON
        } catch (e) {
            displayValue = rawValue; // Not JSON, fallback to string
        }

        output += `${key}:\n${displayValue}\n\n`;
    }

    displayArea.textContent = output || '[Empty]';
}


// Display localStorage contents on page load
window.onload = displayLocalStorage;
</script>

<?php


$indicesServer = array(
    'PHP_SELF', 'argv', 'argc', 'GATEWAY_INTERFACE', 'SERVER_ADDR', 'SERVER_NAME', 'SERVER_SOFTWARE',
    'SERVER_PROTOCOL', 'REQUEST_METHOD', 'REQUEST_TIME', 'REQUEST_TIME_FLOAT', 'QUERY_STRING',
    'DOCUMENT_ROOT', 'HTTP_ACCEPT', 'HTTP_ACCEPT_CHARSET', 'HTTP_ACCEPT_ENCODING', 'HTTP_ACCEPT_LANGUAGE',
    'HTTP_CONNECTION', 'HTTP_HOST', 'HTTP_REFERER', 'HTTP_USER_AGENT', 'HTTPS', 'REMOTE_ADDR',
    'REMOTE_HOST', 'REMOTE_PORT', 'REMOTE_USER', 'REDIRECT_REMOTE_USER', 'SCRIPT_FILENAME',
    'SERVER_ADMIN', 'SERVER_PORT', 'SERVER_SIGNATURE', 'PATH_TRANSLATED', 'SCRIPT_NAME', 'REQUEST_URI',
    'PHP_AUTH_DIGEST', 'PHP_AUTH_USER', 'PHP_AUTH_PW', 'AUTH_TYPE', 'PATH_INFO', 'ORIG_PATH_INFO'
);

echo '<table cellpadding="10" border="1">';
foreach ($indicesServer as $arg) {
    echo '<tr><td>' . $arg . '</td><td>' . (isset($_SERVER[$arg]) ? $_SERVER[$arg] : '-') . '</td></tr>';
}
echo '</table>';

phpinfo();
