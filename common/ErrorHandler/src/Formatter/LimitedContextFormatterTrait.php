<?php

namespace Ddm\ErrorHandler\Formatter;

/**
 * Limits error context from records to prevent memory overflows
 *
 */
trait LimitedContextFormatterTrait {

    /**
     * @see Monolog\Formatter\NormalizerFormatter::normalize()
     *
     * @param array $data
     * @return array $data
     */
    protected function normalize($data) {
        if (is_array($data) && isset($data['context'])) {
            $data['context'] = $this->limitContext($data['context']);
        }
        return parent::normalize($data);
    }

    /**
     * Limit contextual information in error records
     *
     * Primarily this method intercepts and stringifies the context variable $this
     * to prevent common memory overfliows
     *
     * @param mixed $context
     * @return mixed
     */
    protected function limitContext($context) {
        if (is_array($context)) {
            if (isset($context['context'])) {
                $context['context'] = $this->limitContext($context['context']);
            }
            if (isset($context['this']) && is_object($context['this'])) {
                $value = '';
                if ($context['this'] instanceof \Exception) {
                    $value = $this->normalizeException($context['this']);
                } else if (method_exists($context['this'], '__toString') && !$context['this'] instanceof \JsonSerializable) {
                    $value = (string) $context['this'];
                } else if ($context['this'] instanceof \JsonSerializable) {
                    // the rest is json-serialized in some way
                    $value = $this->toJson($data, true);
                }
                $context['this'] = sprintf("[object] (%s: %s)", get_class($context['this']), $value);
            }
        }
        return $context;
    }

}