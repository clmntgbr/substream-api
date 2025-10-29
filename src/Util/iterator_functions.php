<?php

declare(strict_types=1);

if (!function_exists('iterator_to_generator')) {
    /**
     * @template TKey
     * @template TValue
     *
     * @param iterable<TKey, TValue> $iterator
     *
     * @return Generator<TKey, TValue, mixed, void>
     */
    function iterator_to_generator(iterable $iterator): Generator
    {
        yield from $iterator;
    }
}

if (!function_exists('iterator_chunk')) {
    /**
     * Split an iterable into chunks.
     *
     * @template TKey
     * @template TValue
     *
     * @param iterable<TKey, TValue> $iterator      The iterable to be chunked
     * @param int                    $length        The length of each chunk. Must be greater than 0.
     * @param bool                   $preserve_keys Whether to preserve the original keys. Defaults to false.
     *
     * @return Generator<int, Generator<int|TKey, TValue, mixed, void>, void, mixed>
     *
     * @throws ValueError if $length is less than or equal to 0
     */
    function iterator_chunk(iterable $iterator, int $length, bool $preserve_keys = false): Generator
    {
        if ($length <= 0) {
            throw new ValueError('generator_chunk(): Argument #2 ($length) must be greater than 0');
        }

        $generator = iterator_to_generator($iterator);

        return generator_chunk($generator, $length, $preserve_keys);
    }
}

if (!function_exists('generator_chunk')) {
    /**
     * Split a Generator into chunks.
     *
     * @template TKey
     * @template TValue
     * @template TSend
     * @template TReturn
     *
     * @param Generator<TKey, TValue, TSend, TReturn> $generator     The generator to be chunked
     * @param int                                     $length        The length of each chunk. Must be greater than 0.
     * @param bool                                    $preserve_keys Whether to preserve the original keys. Defaults to false.
     *
     * @return Generator<int, Generator<int|TKey, TValue, TSend, void>, void, TReturn>
     *
     * @throws ValueError if $length is less than or equal to 0
     */
    function generator_chunk(Generator $generator, int $length, bool $preserve_keys = false): Generator
    {
        if ($length <= 0) {
            throw new ValueError('generator_chunk(): Argument #2 ($length) must be greater than 0');
        }

        $chunk = static function () use ($generator, $length, $preserve_keys): Generator {
            for ($i = 0; $i < $length && $generator->valid(); ++$i) {
                if ($preserve_keys) {
                    yield $generator->key() => $generator->current();
                } else {
                    yield $generator->current();
                }

                if ($i + 1 < $length) {
                    $generator->next();
                }
            }
        };

        while ($generator->valid()) {
            yield $chunk();
            $generator->next();
        }

        return $generator->getReturn();
    }
}
