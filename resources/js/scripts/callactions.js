/*
Add to twig template:

<script type="text/javascript">
    window.baseUrl = "{{ parseEnv('@web') }}"
    window.actionTrigger = "{{ craft.app.config.general.actionTrigger }}";
    window.csrfTokenName = "{{ craft.app.config.general.csrfTokenName }}";
    window.csrfTokenValue = "{{ craft.app.request.csrfToken }}";
</script>
*/

// Define a function named 'postAction' to handle posting data to the server
window.postAction = function(action, data, callback, handleErrors = true) {
    // Include the CSRF token in the data object
    data[window.csrfTokenName] = window.csrfTokenValue;

    // Use the Fetch API to send a POST request to the server
    fetch(getActionUrl(action), {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then((response) => {
            // Parse the response as JSON and create a promise to handle it
            // https://stackoverflow.com/questions/40248231/how-to-handle-http-code-4xx-responses-in-fetch-api
            return new Promise((resolve) => response.json()
                .then((json) => resolve({
                    status: response.status,
                    ok: response.ok,
                    json,
                })));
        }).then(({status, json, ok}) => {
            const message = json.message;
            switch (status) {
                case 400:
                    // If the response status is 400, handle the error
                    // This code is used by Crafts asFailure/asModelFailure methods
                    console.log(status, json);
                    if (handleErrors) {
                        alert('Error ' + status + ': ' + message);
                    } else {
                        // Call the callback function with the error details
                        callback(json, status, ok);
                    }
                    break;
                case 200:
                    // If the response status is 201 or 200, call the callback function with the response data
                    callback(json, status, ok);
                    break;
                case 500:
                default:
                    // If the response status is 500 or other, log the error and throw an error
                    console.log(status, json);
                    throw new Error(status + ': ' + message);
            }
        })
        .catch(function(error) {
            // Handle any errors that occur during the fetch request or promise handling
            console.log(error);
            alert(error);
        });
}

// Function to get the action URL based on the provided action parameter
function getActionUrl(action) {
    return window.baseUrl + '/' + window.actionTrigger + '/' + action;
}

