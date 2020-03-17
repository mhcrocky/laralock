<?php

/**
 * use libraries
 */

/**
 * use models
 */

/** */

/**
 * response success with data
 *
 * @param string $msg
 * @param array ...$response_data
 * @return void
 */
function successResponse($msg, ...$response_data)
{
    return ['status' => 'success', 'message' => $msg, 'response_data' => $response_data];
}

/**
 * response info
 *
 * @param string $msg
 * @return void
 */
function infoResponse($msg)
{
    return ['status' => 'info', 'message' => $msg];
}

/**
 * response error
 *
 * @param string $msg
 * @return void
 */
function errorResponse($msg)
{
    return ['status' => 'error', 'message' => $msg];
}

/**
 * response custom status with data
 *
 * @param string $stat
 * @param string $msg
 * @param array ...$response_data
 * @return void
 */
function customResponse($stat, $msg, ...$response_data)
{
    return ['status' => $stat, 'message' => $msg, 'response_data' => $response_data];
}

/**
 * response resources with data
 *
 * @param array $response_data
 * @param string $stat
 * @param string $msg
 * @param string $time
 * @return void
 */
function dataResponse($response_data, $stat = '', $msg = '', $time = '')
{
    return [
        'status' => $stat ? $stat : 'success',
        'access' => $time ? $time : Carbon_HumanFullDateTimeNow(),
        'message' => $msg ? $msg : '',
        'response_data' => $response_data
    ];
}

/**
 * response json error status
 */
function _throwErrorResponse($message = '', $code = '')
{
    $setMsg = $message ? $message : "Sorry, you cant find anything here";
    $setCode = $code ? $code : "404";
    return response()->json(errorResponse($setMsg), $setCode);
}
