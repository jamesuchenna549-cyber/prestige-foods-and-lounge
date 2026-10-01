<?php

// =========================
// ERROR HANDLER
// =========================


// Send safe JSON error response
function sendErrorResponse($message, $statusCode = 500)
{
    http_response_code($statusCode);

    echo json_encode([
        "success" => false,
        "message" => $message
    ]);

    exit;
}


// Log errors privately
function logError($error)
{
    $logFile = __DIR__ . "/errors.log";

    $message = "[" . date("Y-m-d H:i:s") . "] ";

    if ($error instanceof Throwable) {
        $message .= $error->getMessage();
        $message .= " in " . $error->getFile();
        $message .= " on line " . $error->getLine();
    } else {
        $message .= $error;
    }

    $message .= PHP_EOL;

    file_put_contents(
        $logFile,
        $message,
        FILE_APPEND
    );
}


// Handle uncaught exceptions
function handleException ($exception){
    logError($exception);

    sendErrorResponse(
        "Something went wrong. Please try again."
    );
}


// Handle PHP errors
function handleError(
    $severity,
    $message,
    $file,
    $line
) {

    $error = new ErrorException(
        $message,
        0,
        $severity,
        $file,
        $line
    );

    logError($error);

    sendErrorResponse(
        "Something went wrong. Please try again."
    );
}


// Activate handlers

set_exception_handler(
    "handleException"
);


set_error_handler(
    "handleError"
);

?>