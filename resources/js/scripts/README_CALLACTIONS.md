# Documentation for `postAction` Function

## Overview
The `postAction` function is a JavaScript function that makes a POST request to a Craft CMS action, passing some data, and then handles the server's response in the client.

## Syntax
```javascript
window.postAction(action, data, callback);
```

### Parameters

- `action` (required): A string representing the specific action on the server that the function will call.

- `data` (required): An object representing the payload to be sent to the server. It should contain key-value pairs representing the data you want to send.

- `callback` (required): A function that will be called if the server returns a successful response that contains no error or exception. This function will be passed the server's response data as its argument.

## Usage

1. **Define the action**: Define the action you want to call on the server. This would typically match the name of a Craft CMS web controller action.

2. **Prepare the data**: Prepare the data you want to send to the server as an object. The keys should be the data field names, and the values should be the data values.

3. **Define the callback function**: Define the function that will be called if the server's response is successful and contains no error or exception.

Here's an example usage:

```javascript
var action = 'myModule/myController/myAction';
var data = {
    field1: 'value1',
    field2: 'value2'
};
var callback = function(responseData) {
    console.log('Server response:', responseData);
};

window.postAction(action, data, callback);
```

In this example, a POST request will be sent to the server endpoint represented by `action` (in this case, 'submitForm'). The data to be sent is the `data` object. If the server's response is successful and contains no error or exception, the `callback` function will be called, and it will log the server's response data.

## Errors and Exceptions

If the server's response contains an error or an exception, an alert will be shown with the error or exception message. If an error occurs while making the request or handling the response, an alert will be shown with the error message.

Please note that the presence of CSRF tokens indicates that the system has security measures in place to protect against cross-site request forgery attacks. The CSRF token's name and value should be stored in `window.csrfTokenName` and `window.csrfTokenValue` respectively before calling `postAction`.