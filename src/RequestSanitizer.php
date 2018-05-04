<?php

namespace Okipa\LaravelRequestSanitizer;

use DataSanitizer;
use Illuminate\Foundation\Http\FormRequest;

class RequestSanitizer extends FormRequest
{
    /**
     * Recursively sanitize the request entries.
     * To check how data will be sanitized, check the used package : https://github.com/Okipa/php-data-sanitizer.
     * Declare this property to false to disable the request entries sanitizing.
     */
    protected $sanitizeEntries = true;
    /**
     * Except the given keys (dot notation accepted) from the request entries sanitizing.
     * It can be a good option when you have numbers beginning with a zero that you want to keep that way, for example.
     */
    protected $exceptFromSanitize = [];
    /**
     * Recursively exclude all the null entries from the request.
     * Declare this property to false to disable the null entries exclusion.
     */
    protected $excludeNullEntries = true;
    /**
     * Except the given keys (dot notation accepted) from the null entries exclusion.
     */
    protected $exceptFromNullExclusion = [];
    /**
     * Set the which request keys (dot notation accepted) should be safety checked.
     * If a given key is declared in this array and is not found in the request, it will take « false » for value.
     */
    protected $safetyCheckBooleanValues = [];

    /**
     * @param array                $query      The GET parameters
     * @param array                $request    The POST parameters
     * @param array                $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array                $cookies    The COOKIE parameters
     * @param array                $files      The FILES parameters
     * @param array                $server     The SERVER parameters
     * @param string|resource|null $content    The raw body data
     */
    public function __construct(
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null
    ) {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
        if (method_exists($this, 'before')) {
            $this->before();
        }
        if ($this->sanitizeEntries) {
            $this->replace($this->sanitizeEntries());
        }
        if ($this->excludeNullEntries) {
            $this->replace($this->excludeNullEntries());
        }
        $this->replace($this->checkEntriesBooleanValues());
    }

    /**
     * Sanitize the request entries
     *
     * @return array
     */
    protected function sanitizeEntries()
    {
        $sanitizedEntries = DataSanitizer::sanitize($this->except($this->exceptFromSanitize));
        $mergedSanitizedEntries = array_replace_recursive($this->all(), $sanitizedEntries);

        return $mergedSanitizedEntries;
    }

    /**
     * Exclude null entries from the request
     *
     * @return array
     */
    protected function excludeNullEntries()
    {
        $filteredEntries = [];
        $this->excludeNullEntriesProcess($this->except($this->exceptFromNullExclusion), $filteredEntries);
        $mergedExcludedNullEntries = array_replace_recursive($this->all(), $filteredEntries);

        return $mergedExcludedNullEntries;
    }

    /**
     * Recursively exclude null entries from the given array
     *
     * @param array $entries
     * @param array $filteredEntries
     */
    protected function excludeNullEntriesProcess(array $entries, array &$filteredEntries)
    {
        foreach ($entries as $key => $entry) {
            if (! is_null($entry)) {
                if (is_array($entry)) {
                    $arrayEntries = [];
                    $this->excludeNullEntriesProcess($entry, $arrayEntries);
                    $filteredEntries[$key] = $arrayEntries;
                } else {
                    $filteredEntries[$key] = $entry;
                }
            }
        }
    }

    /**
     * Check if the given boolean values are given and set them to false if this is not the case.
     */
    protected function checkEntriesBooleanValues()
    {
        $toMerge = [];
        foreach ($this->safetyCheckBooleanValues as $booleanValueKey) {
            $value = $this->input($booleanValueKey, false);
            array_set($toMerge, $booleanValueKey, $value);
        }
        $mergedBooleanValuesEntries = array_replace_recursive($this->all(), $toMerge);

        return $mergedBooleanValuesEntries;
    }
}
