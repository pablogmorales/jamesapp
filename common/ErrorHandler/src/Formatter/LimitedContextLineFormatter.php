<?php

namespace Ddm\ErrorHandler\Formatter;

use Monolog\Formatter\LineFormatter;

/**
 * Limits error context from records to prevent memory overflows
 *
 */
class LimitedContextLineFormatter extends LineFormatter {

    use LimitedContextFormatterTrait;

}