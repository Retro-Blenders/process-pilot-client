<?php

namespace ProcessPilot\Client;

use ProcessPilot\Client\Exception\CompileErrorException;
use ProcessPilot\Client\Exception\CompileWarningException;
use ProcessPilot\Client\Exception\CoreErrorException;
use ProcessPilot\Client\Exception\CoreWarningException;
use ProcessPilot\Client\Exception\DeprecatedException;
use ProcessPilot\Client\Exception\FatalErrorException;
use ProcessPilot\Client\Exception\NoticeException;
use ProcessPilot\Client\Exception\ParseException;
use ProcessPilot\Client\Exception\ProcessPilotException;
use ProcessPilot\Client\Exception\RecoverableErrorException;
use ProcessPilot\Client\Exception\StrictException;
use ProcessPilot\Client\Exception\UnknownException;
use ProcessPilot\Client\Exception\UserDeprecatedException;
use ProcessPilot\Client\Exception\UserErrorException;
use ProcessPilot\Client\Exception\UserNoticeException;
use ProcessPilot\Client\Exception\UserWarningException;
use ProcessPilot\Client\Exception\WarningException;
use ProcessPilot\Client\Service\PilotClientService;
use Throwable;

final class ErrorHandler
{
    /**
     * @var ?callable
     */
    public static $previousExceptionCallback = null;

    /**
     * @var ?callable
     */
    public static $previousErrorHandlerCallback = null;

    public function __construct(private readonly PilotClientService $pilotClientService)
    {
    }

    public function register(): bool
    {
        register_shutdown_function( function () {
            $error = error_get_last();

            if ($error["type"] === E_ERROR) {
                self::logError( $error["type"], $error["message"], $error["file"], $error["line"] );
            }
        });

        self::$previousExceptionCallback = set_exception_handler( [self::class, 'logException'] );
        self::$previousErrorHandlerCallback = set_error_handler([self::class, 'logError'], E_ALL);

        return true;
    }

    public function logException(Throwable $e): void
    {
        $this->pilotClientService->sendToServer($e);

        if (is_callable(self::$previousExceptionCallback) && !$e::class instanceof ProcessPilotException) {
            call_user_func(self::$previousExceptionCallback, $e);
        }
    }

    public function logError(int $err_severity, string $err_msg, string $err_file, int $err_line): bool
    {
        switch($err_severity)
        {
            case E_ERROR:
                self::logException(new FatalErrorException($err_msg, 0, $err_severity, $err_file, $err_line));
                break;
            case E_WARNING:
                self::logException(new WarningException($err_msg, 0, $err_severity, $err_file, $err_line));
                break;
            case E_PARSE:
                self::logException(new ParseException($err_msg, 0, $err_severity, $err_file, $err_line));
                break;
            case E_NOTICE:
                self::logException(new NoticeException($err_msg, 0, $err_severity, $err_file, $err_line));
                break;
            case E_CORE_ERROR:
                self::logException(new CoreErrorException($err_msg, 0, $err_severity, $err_file, $err_line));
                break;
            case E_CORE_WARNING:
                self::logException(new CoreWarningException($err_msg, 0, $err_severity, $err_file, $err_line));
                break;
            case E_COMPILE_ERROR:
                self::logException(new CompileErrorException($err_msg, 0, $err_severity, $err_file, $err_line));
                break;
            case E_COMPILE_WARNING:
                self::logException(new CompileWarningException($err_msg, 0, $err_severity, $err_file, $err_line));
                break;
            case E_USER_ERROR:
                self::logException(new UserErrorException($err_msg, 0, $err_severity, $err_file, $err_line));
                break;
            case E_USER_WARNING:
                self::logException(new UserWarningException($err_msg, 0, $err_severity, $err_file, $err_line));
                break;
            case E_USER_NOTICE:
                self::logException(new UserNoticeException($err_msg, 0, $err_severity, $err_file, $err_line));
                break;
            case E_STRICT:
                self::logException(new StrictException($err_msg, 0, $err_severity, $err_file, $err_line));
                break;
            case E_RECOVERABLE_ERROR:
                self::logException(new RecoverableErrorException($err_msg, 0, $err_severity, $err_file, $err_line));
                break;
            case E_DEPRECATED:
                self::logException(new DeprecatedException($err_msg, 0, $err_severity, $err_file, $err_line));
                break;
            case E_USER_DEPRECATED:
                self::logException(new UserDeprecatedException($err_msg, 0, $err_severity, $err_file, $err_line));
                break;
            default:
                self::logException(new UnknownException($err_msg, 0, $err_severity, $err_file, $err_line));
                break;
        }

        // Pass errors to the previous error handler
        if (is_callable(self::$previousErrorHandlerCallback)) {
            call_user_func(self::$previousErrorHandlerCallback, $err_severity, $err_msg, $err_file, $err_line);
        }

        return false;
    }
}
