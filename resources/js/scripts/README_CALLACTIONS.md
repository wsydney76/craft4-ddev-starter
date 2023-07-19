## `postAction` Method Documentation

The `postAction` method is a utility function that simplifies sending a controller action request to the server and handling the response. It uses the Fetch API to perform the network request and provides a convenient way to handle success and error scenarios.

### Method Signature

```javascript
window.postAction(action: string, data: object, callback: function, handleErrors: boolean = true): void
```

### Parameters

- `action` (string): The controller action to be performed on the server, in the form of `<module/plugin>/<controllerId>/<actionId>`.

- `data` (object): The data to be sent along with the request. It should be an object containing the necessary parameters and values.

- `callback` (function): A callback function to be executed after a successful response from the server. It receives the parsed JSON response, the status code, and a boolean indicating the success status.

- `handleErrors` (boolean, optional): Specifies whether to handle HTTP errors automatically or allow the callback function to handle them. By default, it's set to `true`.

### Usage

To use the `postAction` method, follow these steps:

1. Ensure that the `postAction` function is available in the global scope, either by including it in your JavaScript code or by loading the script that contains the function.

2. Call the `postAction` function and provide the required parameters:

```javascript
postAction(action, data, callback, handleErrors);
```

- `action` should be a string specifying the server action or endpoint you want to invoke.

- `data` should be an object containing the necessary data to send to the server.

- `callback` should be a function that will be called after a successful response from the server. It will receive the parsed JSON response, the status code, and a boolean indicating the success status.

- `handleErrors` is an optional parameter. If set to `true` (default), it will display an alert with the error message for HTTP error responses. If set to `false`, the error handling will be delegated to the callback function.

### Example

Here's an example that demonstrates how to use the `postAction` method:

```javascript
// Define a callback function to handle the server response
// If the handleErrors parameter is set to true, this function will not be called for HTTP errors, so status will always be 200, and success will always be true
function handleResponse(data, status, success) {
    if (success) {
        console.log('Success:', data);
        // Handle the successful response
    } else {
        console.log('Error:', status, data);
        // Handle the error response
    }
}

// Prepare the data to send
var postData = {
    name: 'John Doe',
    email: 'johndoe@example.com'
};

// Call the postAction method
postAction('submitForm', postData, handleResponse, false);
```

In this example, the `postAction` method is called with the action `'submitForm'` and the `postData` object. The `handleResponse` function is provided as the callback, which will be called with the server response, status code, and success status.

That's it! You can now use the `postAction` method to easily send POST requests to the server and handle the responses in your JavaScript code.