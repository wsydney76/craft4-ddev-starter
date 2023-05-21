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

window.postAction = function(action, data, callback) {
    data[window.csrfTokenName] = window.csrfTokenValue;

    fetch(getActionUrl(action), {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.error) {
                alert('Error ' + data.status + ': ' + data.error);
            } else if (data.exception) {
                alert('Exception: ' + data.exception)
            } else {
                callback(data);
            }
        })
        .catch(function(error) {
            console.log(error);
            alert('Systemfehler: ' + error)
        });
}

function getActionUrl(action) {
    return window.baseUrl + '/' + window.actionTrigger + '/' + action;
}