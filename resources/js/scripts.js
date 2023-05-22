/*
Add to twig template:

<script type="text/javascript">
    window.baseUrl = "{{ parseEnv('@web') }}"
    window.actionTrigger = "{{ craft.app.config.general.actionTrigger }}";
    window.csrfTokenName = "{{ craft.app.config.general.csrfTokenName }}";
    window.csrfTokenValue = "{{ craft.app.request.csrfToken }}";
</script>
*/

window.fetchJson = function(url, callback) {
    fetch(getActionUrl(url), {
        headers: {
            'Accept': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else if (data.exception) {
                alert('Exception: ' + data.exception)
            } else {
                callback(data);
            }
        })
        .catch(function(error) {
            console.log(error);
            alert('Error:', error)
        });
}

// Define the postAction function on the window object.
window.postAction = function(action, data, callback) {

    // Add the CSRF token to the data payload for security.
    data[window.csrfTokenName] = window.csrfTokenValue;

    // Use the Fetch API to make a POST request.
    fetch(getActionUrl(action), {
        method: 'POST', // The HTTP method is POST.
        headers: {
            'Accept': 'application/json', // The client will accept a JSON response.
            'Content-Type': 'application/json' // The sent data is in JSON format.
        },
        body: JSON.stringify(data) // The data payload is converted to a JSON string.
    })
        // Convert the response to JSON.
        .then(response => response.json())
        .then(data => {
            console.log(data); // Log the response data.

            // Check for an error in the response.
            if (data.error) {
                alert('Error ' + data.status + ': ' + data.error); // Show an alert with the error message.
            }
            // Check for an exception in the response.
            else if (data.exception) {
                alert('Exception: ' + data.exception); // Show an alert with the exception message.
            }
            // If there's no error or exception, call the callback function with the response data.
            else {
                callback(data);
            }
        })
        // Handle any error that might occur during the request.
        .catch(function(error) {
            console.log(error); // Log the error.
            alert('System error: ' + error); // Show an alert with the error message.
        });
}

// Define the getActionUrl function.
function getActionUrl(action) {
    // Construct the URL for the request by combining the base URL, action trigger, and action.
    return window.baseUrl + '/' + window.actionTrigger + '/' + action;
}
