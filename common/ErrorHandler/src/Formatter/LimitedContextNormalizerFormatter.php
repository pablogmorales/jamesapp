<?php

namespace Ddm\ErrorHandler\Formatter;

use Monolog\Formatter\NormalizerFormatter;

/**
 * Limits error context from records to prevent memory overflows
 *
 */
class LimitedContextNormalizerFormatter extends NormalizerFormatter {

    use LimitedContextFormatterTrait;

}