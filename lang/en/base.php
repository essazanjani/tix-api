<?php

return [
    'exceptions' => [
        'validation' => 'A validation error has occurred.',
        'token_mismatch' => 'Your validation token is invalid.',
        'query' => 'An error occurred in the provided query.',
        'internal_server' => 'An error occurred in your application.',
        'model_not_found' => 'The requested entity was not found.',
        'authorization' => 'An authorization error occurred for your request.',
        'authentication' => 'An authentication error occurred for your request.',
        'method_not_allowed_http' => 'The requested method is not allowed.',
        'not_found_http' => 'The requested path was not found on the server.',
        'view' => 'An error occurred in the view page.',
        'type_error' => 'The type of the sent parameter is invalid.',
        'throttle_request' => 'You have exceeded the request limit. Please wait a few minutes.',
        'backed_enum_case_not_found' => 'The specified enum case was not found.',
        'bad_request' => 'Invalid request.'
    ],

    'messages' => [
        'success' => 'The operation was completed successfully.',
        'created' => 'The operation was created successfully.',
        'updated' => 'The operation was updated successfully.',
        'deleted' => 'The operation was deleted successfully.'
    ],

    'errors' => [
        'ticket_closed' => 'The ticket has been closed.',
    ]
];
